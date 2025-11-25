<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Infrastructure\Persist\Repository\UserRepository;
use App\Exception\UserBusinessException;
use App\Models\User;

class EloquentUserRepository extends EloquentBaseRepository implements UserRepository
{
    public function save(User $user): void
    {
        $user->save();
    }

    public function getByUserName(string $username): User
    {
        $user = $this->model->query()->where('username', $username)->first();
        if (is_null($user)){
            throw UserBusinessException::userDoesNotExist();
        }

        return $user;
    }

    public function existsByUsername(string $username): bool
    {
        return $this->model->where('username', $username)->exists();
    }

    public function getByID(int $id): User
    {
        $user = $this->model->query()->where('id', $id)->first();
        if (is_null($user)){
            throw UserBusinessException::userDoesNotExist();
        }

        return $user;
    }
}