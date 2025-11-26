<?php

namespace App\Exception;

class TicketException extends BusinessException
{
    public static function invalidPerPage(): self
    {
        return new self('invalid per page');
    }
    
    public static function invalidID(): self
    {
        return new self('invalid id');
    }

    public static function canNotBeApproved(): self
    {
        return new self('can not be approved');
    }

    public static function canNotHaveActionOnTicket(): self
    {
        return new self('can not have action on ticket');
    }
}