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
        $user = $this->model->query()->where("username", $username)->first();
        if (is_null($user)){
            throw UserBusinessException::userDoesNotExist();
        }

        return $user;
    }   
}