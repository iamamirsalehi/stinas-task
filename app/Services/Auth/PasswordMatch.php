<?php

namespace App\Services\Auth;

use Illuminate\Contracts\Hashing\Hasher;

class PasswordMatch
{
    public function __construct(private Hasher $hasher)
    {
    }

    public function isTheSame(string $actual, string $hash): bool
    {
        return $this->hasher->check($actual, $hash);
    }
}