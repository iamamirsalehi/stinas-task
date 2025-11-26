<?php

namespace App\Services\Auth;

use App\Models\Admin;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AdminLoginSessionGenerator
{
    public function __construct(private AuthFactory $auth)
    {
    }

    public function login(Admin $admin): void
    {
        $this->auth->guard('admin')->login($admin);
    }
}

