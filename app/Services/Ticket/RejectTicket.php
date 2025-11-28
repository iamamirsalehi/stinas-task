<?php

namespace App\Services\Ticket;

use App\Models\Admin;

readonly class RejectTicket
{
    public function __construct(
        public int $ticketID,
        public Admin $admin,
        public ?string $note = null,
    )
    {
    }
}

