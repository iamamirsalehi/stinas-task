<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Exception\TicketApproveException;
use App\Infrastructure\Persist\Repository\TicketApproveStepRepository;
use App\Models\TicketApproveStep;

class EloquentTicketApproveStepRepository extends EloquentBaseRepository implements TicketApproveStepRepository
{
    public function getByOrder(int $order): TicketApproveStep
    {
        $ticketApproveStep =  $this->model->query()->where('order', $order)->first();
        if (is_null($ticketApproveStep)){
            throw TicketApproveException::ticketDoesNotExist();
        }

        return $ticketApproveStep;
    }

    public function findByOrder(int $order): ?TicketApproveStep
    {
        return $this->model->query()->where('order', $order)->first();
    }
}

