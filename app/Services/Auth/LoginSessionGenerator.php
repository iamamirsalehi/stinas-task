<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginSessionGenerator
{
    public function __construct(private Auth $auth)
    {
    }

    public function login(User $user): void
    {
        $this->auth->login($user);
    }
}