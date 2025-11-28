<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TicketRepository
{
    public function save(Ticket $ticket);

    public function list(int $perPage = 10, $page = 1, array $statuses = []): LengthAwarePaginator;

    public function getByID(int $id): Ticket;
}