<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\Ticket;


interface TicketRepository
{
    public function save(Ticket $ticket);
}