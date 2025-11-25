<?php

namespace App\Exception;

class TicketException extends BusinessException
{
    public static function invalidPerPage(): self
    {
        return new self('invalid per page');
    }
}