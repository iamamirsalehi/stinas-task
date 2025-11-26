<?php

namespace App\Services\Ticket;

use App\Models\Admin;

readonly class ApproveTicket
{
    public function __construct(
        public int $ticketID,
        public string $note,
        public Admin $admin,
    )
    {
    }
}