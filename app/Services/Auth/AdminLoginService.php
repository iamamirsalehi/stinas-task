<?php

namespace App\Services\Auth;

use App\Exception\AdminException;
use App\Infrastructure\Persist\Repository\AdminRepository;

class AdminLoginService
{
    public function __construct(
        private AdminRepository $adminRepository,
        private PasswordMatch $passwordMatch,
        private AdminLoginSessionGenerator $loginSessionGenerator,
    )
    {
    }

    public function login(string $username, string $password): void
    {
        $admin = $this->adminRepository->getByUserName($username);

        if (!$this->passwordMatch->isTheSame($password, $admin->password)){
            throw AdminException::usernameOrPasswordIsInvalid();
        }

        $this->loginSessionGenerator->login($admin);
    }
}

