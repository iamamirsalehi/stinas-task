<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Exception\TicketApproveException;
use App\Infrastructure\Persist\Repository\TicketApproveRepository;
use App\Models\TicketApprove;

class EloquentTicketApproveRepository extends EloquentBaseRepository implements TicketApproveRepository
{
    public function getByOrder(int $order): TicketApprove
    {
        $ticketApprove =  $this->model->query()->where('order', $order)->first();
        if (is_null($ticketApprove)){
            throw TicketApproveException::ticketDoesNotExist();
        }

        return $ticketApprove;
    }

    public function findByOrder(int $order): ?TicketApprove
    {
        return $this->model->query()->where('order', $order)->first();
    }
}