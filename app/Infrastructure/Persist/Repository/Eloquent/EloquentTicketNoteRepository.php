<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Infrastructure\Persist\Repository\TicketNoteRepository;
use App\Models\TicketNote;

class EloquentTicketNoteRepository extends EloquentBaseRepository implements TicketNoteRepository
{
    public function save(TicketNote $ticketNote): void
    {
        $ticketNote->save();
    }
}