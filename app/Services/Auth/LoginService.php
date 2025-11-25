<?php

namespace App\Services\Auth;

use App\Exception\UserBusinessException;
use App\Infrastructure\Persist\Repository\UserRepository;

class LoginService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordMatch $passwordMatch,
        private LoginSessionGenerator $loginSessionGenerator,
    )
    {
    }

    public function login(string $username, string $password): void
    {
        $user = $this->userRepository->getByUserName($username);

        if (!$this->passwordMatch->isTheSame($password, $user->password)){
            throw UserBusinessException::usernameOrPasswordIsInvalid();
        }

        $this->loginSessionGenerator->login($user);
    }
}