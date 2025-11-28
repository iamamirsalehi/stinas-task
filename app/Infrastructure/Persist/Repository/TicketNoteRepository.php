<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\TicketNote;

interface TicketNoteRepository
{
    public function save(TicketNote $ticketNote): void;
}