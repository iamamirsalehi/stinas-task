<?php

namespace App\Exception;

class AdminException extends BusinessException
{
    public static function adminDoesNotExist(): self
    {
        return new self("admin does not exist");
    }
}