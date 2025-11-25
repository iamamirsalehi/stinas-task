<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Models\Ticket;

class EloquentTicketRepository extends EloquentBaseRepository implements TicketRepository
{
    public function save(Ticket $ticket): void
    {
        $ticket->save();
    }
}