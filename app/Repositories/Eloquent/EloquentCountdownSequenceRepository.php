<?php

namespace App\Repositories\Eloquent;

use App\Models\CountdownSequence;
use App\Models\CountdownShare;
use App\Repositories\Contracts\CountdownSequenceRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentCountdownSequenceRepository implements CountdownSequenceRepositoryInterface
{
    public function allForUser(int $userId): Collection
    {
        return CountdownSequence::query()
            ->where('user_id', $userId)
            ->with(['items.countdown', 'share'])
            ->latest()
            ->get();
    }

    public function findOrFail(int $sequenceId): CountdownSequence
    {
        return CountdownSequence::query()->with(['items.countdown', 'share'])->findOrFail($sequenceId);
    }

    public function findByToken(string $token): ?CountdownShare
    {
        return CountdownShare::query()
            ->where('token', $token)
            ->with(['sequence.items.countdown', 'sequence.share'])
            ->first();
    }

    public function create(array $attributes, array $items): CountdownSequence
    {
        return DB::transaction(function () use ($attributes, $items): CountdownSequence {
            $sequence = CountdownSequence::query()->create($attributes);

            $this->syncItems($sequence, $items);
            $this->issueShareToken($sequence);

            return $sequence->load(['items.countdown', 'share']);
        });
    }

    public function update(CountdownSequence $sequence, array $attributes, array $items): CountdownSequence
    {
        return DB::transaction(function () use ($sequence, $attributes, $items): CountdownSequence {
            $sequence->fill($attributes);
            $sequence->save();

            $sequence->items()->delete();
            $this->syncItems($sequence, $items);

            return $sequence->load(['items.countdown', 'share']);
        });
    }

    public function issueShareToken(CountdownSequence $sequence): CountdownShare
    {
        return CountdownShare::query()->updateOrCreate(
            ['countdown_sequence_id' => $sequence->id],
            ['token' => (string) Str::uuid()]
        );
    }

    private function syncItems(CountdownSequence $sequence, array $items): void
    {
        foreach (array_values($items) as $position => $item) {
            $sequence->items()->create([
                'countdown_id' => $item['countdown_id'],
                'position' => $position + 1,
            ]);
        }
    }
}
