<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\User;

interface UserRepository
{
    public function save(User $user): void;

    public function getByUserName(string $username): User;

    public function existsByUsername(string $username): bool;

    public function getByID(int $id): User;
}