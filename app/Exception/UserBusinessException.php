<?php

namespace App\Exception;

class UserBusinessException extends BusinessException
{
    public static function userDoesNotExist(): self
    {
        return new self('user does not exist');
    }

    public static function usernameOrPasswordIsInvalid(): self
    {
        return new self('username or password is invalid');
    }

    public static function usernameAlreadyExists(): self
    {
        return new self('username already exists');
    }
}