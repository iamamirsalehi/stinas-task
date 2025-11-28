<?php

namespace App\Exception;

class TicketApproveException extends BusinessException
{
    public static function ticketDoesNotExist()
    {
        return new self('ticket does not exist');
    }
}