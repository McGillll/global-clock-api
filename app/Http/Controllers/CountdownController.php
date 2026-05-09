<?php

namespace App\Http\Controllers;

use App\Events\CountdownSequenceUpdated;
use App\Models\Countdown;
use App\Models\CountdownSequence;
use App\Repositories\Contracts\CountdownRepositoryInterface;
use App\Repositories\Contracts\CountdownSequenceRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountdownController extends Controller
{
    public function __construct(
        private readonly CountdownRepositoryInterface $countdownRepository,
        private readonly CountdownSequenceRepositoryInterface $sequenceRepository,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->countdownRepository->allForUser((int) ($request->user()?->id ?? 1)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $attributes = $request->validate([
            'user_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'background_color' => ['sometimes', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'font_color' => ['sometimes', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $attributes['user_id'] = $attributes['user_id'] ?? (int) ($request->user()?->id ?? 1);

        return response()->json([
            'data' => $this->countdownRepository->create($attributes),
        ], 201);
    }

    public function show(Countdown $countdown): JsonResponse
    {
        return response()->json([
            'data' => $countdown->load('sequenceItems'),
        ]);
    }

    public function update(Request $request, Countdown $countdown): JsonResponse
    {
        $attributes = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'duration_seconds' => ['sometimes', 'integer', 'min:1'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'background_color' => ['sometimes', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'font_color' => ['sometimes', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        return response()->json([
            'data' => $this->countdownRepository->update($countdown, $attributes),
        ]);
    }

    public function destroy(Countdown $countdown): JsonResponse
    {
        $this->countdownRepository->delete($countdown);

        return response()->json(status: 204);
    }

    public function sequenceIndex(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->sequenceRepository->allForUser((int) ($request->user()?->id ?? 1)),
        ]);
    }

    public function sequenceStore(Request $request): JsonResponse
    {
        $attributes = $request->validate([
            'user_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'is_running' => ['sometimes', 'boolean'],
            'current_item_index' => ['sometimes', 'integer', 'min:0'],
            'loop_count' => ['sometimes', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.countdown_id' => ['required', 'integer', 'exists:countdowns,id'],
        ]);

        $items = $attributes['items'];
        unset($attributes['items']);
        $attributes['user_id'] = $attributes['user_id'] ?? (int) ($request->user()?->id ?? 1);
        $attributes['slug'] = $attributes['slug'] ?? str($attributes['name'])->slug()->append('-', uniqid())->toString();

        return response()->json([
            'data' => $this->sequenceRepository->create($attributes, $items),
        ], 201);
    }

    public function sequenceShow(CountdownSequence $sequence): JsonResponse
    {
        return response()->json([
            'data' => $sequence->load(['items.countdown', 'share']),
            'now' => now()->timestamp * 1000,
        ]);
    }

    public function sequenceUpdate(Request $request, CountdownSequence $sequence): JsonResponse
    {
        $attributes = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'is_running' => ['sometimes', 'boolean'],
            'current_item_index' => ['sometimes', 'integer', 'min:0'],
            'started_at' => ['sometimes', 'nullable', 'date'],
            'paused_at' => ['sometimes', 'nullable', 'date'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.countdown_id' => ['required_with:items', 'integer', 'exists:countdowns,id'],
        ]);

        $items = $attributes['items'] ?? [];
        unset($attributes['items']);

        return response()->json([
            'data' => $this->sequenceRepository->update($sequence, $attributes, $items),
        ]);
    }

    public function shareSequence(CountdownSequence $sequence): JsonResponse
    {
        return response()->json([
            'data' => $this->sequenceRepository->issueShareToken($sequence),
        ]);
    }

    public function sharedShow(string $token): JsonResponse
    {
        $share = $this->sequenceRepository->findByToken($token);

        abort_if($share === null, 404);

        return response()->json([
            'data' => $share,
            'now' => now()->timestamp * 1000,
        ]);
    }

    public function sequenceStart(Request $request, CountdownSequence $sequence): JsonResponse
    {
        $sequence->update([
            'status' => 'running',
            'started_at' => now(),
            'paused_at' => null,
            'current_item_position' => 0,
            'paused_seconds' => 0,
        ]);

        $sequence = $sequence->load(['items.countdown', 'share']);
        CountdownSequenceUpdated::dispatch($sequence);

        return response()->json([
            'data' => $sequence,
            'now' => now()->timestamp * 1000,
        ]);
    }

    public function sequencePause(CountdownSequence $sequence): JsonResponse
    {
        $sequence->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        $sequence = $sequence->load(['items.countdown', 'share']);
        CountdownSequenceUpdated::dispatch($sequence);

        return response()->json([
            'data' => $sequence,
        ]);
    }

    public function sequenceResume(CountdownSequence $sequence): JsonResponse
    {
        $sequence->update([
            'status' => 'running',
            'paused_at' => null,
        ]);

        $sequence = $sequence->load(['items.countdown', 'share']);
        CountdownSequenceUpdated::dispatch($sequence);

        return response()->json([
            'data' => $sequence,
        ]);
    }

    public function sequenceDestroy(CountdownSequence $sequence): JsonResponse
    {
        $sequence->items()->delete();
        $sequence->share()->delete();
        $sequence->delete();

        return response()->json(status: 204);
    }

    public function sequenceStop(CountdownSequence $sequence): JsonResponse
    {
        $sequence->update([
            'status' => 'pending',
            'started_at' => null,
            'paused_at' => null,
            'current_item_position' => 0,
            'paused_seconds' => 0,
        ]);

        $sequence = $sequence->load(['items.countdown', 'share']);
        CountdownSequenceUpdated::dispatch($sequence);

        return response()->json([
            'data' => $sequence,
            'now' => now()->timestamp * 1000,
        ]);
    }

}
