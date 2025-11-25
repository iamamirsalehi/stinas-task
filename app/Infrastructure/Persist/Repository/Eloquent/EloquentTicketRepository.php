<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTicketRepository extends EloquentBaseRepository implements TicketRepository
{
    public function save(Ticket $ticket): void
    {
        $ticket->save();
    }

    public function list(int $perPage = 10, $page = 1): LengthAwarePaginator
    {
        return $this->model->query()->paginate($perPage, ['*'], 'page', $page);
    }
}