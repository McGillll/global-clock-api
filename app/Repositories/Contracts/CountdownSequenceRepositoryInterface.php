<?php

namespace App\Repositories\Contracts;

use App\Models\CountdownSequence;
use App\Models\CountdownShare;
use Illuminate\Support\Collection;

interface CountdownSequenceRepositoryInterface
{
    public function allForUser(int $userId): Collection;

    public function findOrFail(int $sequenceId): CountdownSequence;

    public function findByToken(string $token): ?CountdownShare;

    public function create(array $attributes, array $items): CountdownSequence;

    public function update(CountdownSequence $sequence, array $attributes, array $items): CountdownSequence;

    public function issueShareToken(CountdownSequence $sequence): CountdownShare;
}
