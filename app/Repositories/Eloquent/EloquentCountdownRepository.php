<?php

namespace App\Repositories\Eloquent;

use App\Models\Countdown;
use App\Repositories\Contracts\CountdownRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentCountdownRepository implements CountdownRepositoryInterface
{
    public function allForUser(int $userId): Collection
    {
        return Countdown::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function findOrFail(int $countdownId): Countdown
    {
        return Countdown::query()->findOrFail($countdownId);
    }

    public function create(array $attributes): Countdown
    {
        return Countdown::query()->create($attributes);
    }

    public function update(Countdown $countdown, array $attributes): Countdown
    {
        $countdown->fill($attributes);
        $countdown->save();

        return $countdown->refresh();
    }

    public function delete(Countdown $countdown): void
    {
        $countdown->delete();
    }
}
