<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Exception\TicketException;
use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTicketRepository extends EloquentBaseRepository implements TicketRepository
{
    public function save(Ticket $ticket): void
    {
        $ticket->save();
    }

    public function list(int $perPage = 10, $page = 1, array $statuses = []): LengthAwarePaginator
    {
        return $this->model->query()
            ->with('user')
            ->when($statuses, function ($query) use ($statuses) {
                $query->whereIn('status', $statuses);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getByID(int $id): Ticket
    {
        $ticket = $this->model->query()
            ->with(['user'])
            ->find($id);

        if (is_null($ticket)){
            throw TicketException::invalidID();
        }

        return $ticket;
    }
}