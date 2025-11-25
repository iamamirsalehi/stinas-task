<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Hash;

class PasswordMatch
{
    public function __construct(private Hash $hash)
    {
    }

    public function isTheSame(string $actual, string $hash): bool
    {
        return $this->hash->check($actual, $hash);
    }
}