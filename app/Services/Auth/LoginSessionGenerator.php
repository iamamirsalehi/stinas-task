<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class LoginSessionGenerator
{
    public function __construct(private AuthFactory $auth)
    {
    }

    public function login(User $user): void
    {
        $this->auth->guard()->login($user);
    }
}