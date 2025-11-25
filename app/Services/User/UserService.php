<?php

namespace App\Services\User;

use App\Exception\UserBusinessException;
use App\Infrastructure\Persist\Repository\UserRepository;
use App\Models\User;

class UserService
{
    public function __construct(private UserRepository $userRepository)
    {}

    public function add(string $username, string $password): User
    {
        if ($this->userRepository->existsByUsername($username)) {
            throw UserBusinessException::usernameAlreadyExists();
        }

        $user = User::new($username, $password);

        $this->userRepository->save($user);

        return $user;
    }
}