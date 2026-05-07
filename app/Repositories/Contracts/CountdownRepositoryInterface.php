<?php

namespace App\Repositories\Contracts;

use App\Models\Countdown;
use Illuminate\Support\Collection;

interface CountdownRepositoryInterface
{
    public function allForUser(int $userId): Collection;

    public function findOrFail(int $countdownId): Countdown;

    public function create(array $attributes): Countdown;

    public function update(Countdown $countdown, array $attributes): Countdown;

    public function delete(Countdown $countdown): void;
}
