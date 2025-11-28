<?php

namespace App\Services\Ticket;

use App\Infrastructure\Persist\Repository\TicketApproveStepRepository;
use App\Models\Ticket;
use App\Models\TicketApproveStep;

class TicketApproveStepService
{
    public function __construct(
        private TicketApproveStepRepository $ticketApproveStepRepository,
    )
    {
    }

    public function getApprove(Ticket $ticket): ?TicketApproveStep
    {
        if (!$ticket->isApproved()){
            return $this->ticketApproveStepRepository->getByOrder(1);
        }

        $nextApproveOrder = $ticket->ticketApprove->order + 1;

        $nextApprove = $this->ticketApproveStepRepository->findByOrder($nextApproveOrder);

        return $nextApprove;
    }
}

