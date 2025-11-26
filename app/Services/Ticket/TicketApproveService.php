<?php

namespace App\Services\Ticket;

use App\Infrastructure\Persist\Repository\TicketApproveRepository;
use App\Models\Ticket;
use App\Models\TicketApprove;

class TicketApproveService
{
    public function __construct(
        private TicketApproveRepository $ticketApproveRepository,
    )
    {
    }

    public function getApprove(Ticket $ticket): ?TicketApprove
    {
        if (!$ticket->isApproved()){
            return $this->ticketApproveRepository->getByOrder(1);
        }

        $nextApproveOrder = $ticket->ticketApprove->order + 1;

        $nextApprove = $this->ticketApproveRepository->findByOrder($nextApproveOrder);

        return $nextApprove;
    }
}