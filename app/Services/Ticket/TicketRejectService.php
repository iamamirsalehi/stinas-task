<?php

namespace App\Services\Ticket;

use App\Enums\TicketStatus;
use App\Events\TicketRejectedEvent;
use App\Exception\TicketException;
use App\Infrastructure\Bus\EventBus;
use App\Infrastructure\Persist\Repository\TicketNoteRepository;
use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Models\TicketNote;

class TicketRejectService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private TicketNoteRepository $ticketNoteRepository,
        private TicketApproveStepService $ticketApproveStepService,
        private EventBus $eventBus,
    )
    {}

    public function reject(RejectTicket $rejectTicket): void
    {
        $ticket = $this->ticketRepository->getByID($rejectTicket->ticketID);

        $approve = $this->ticketApproveStepService->getApprove($ticket);

        if (is_null($approve)){
            throw TicketException::canNotHaveActionOnTicket();
        }

        $rejectStatus = match($approve->order) {
            1 => TicketStatus::RejectedByAdmin1,
            2 => TicketStatus::RejectedByAdmin2,
            default => throw TicketException::canNotHaveActionOnTicket(),
        };

        $ticket->reject($rejectStatus);

        $this->ticketRepository->save($ticket);

        $ticketNote = TicketNote::new($rejectTicket->note, $ticket, $rejectTicket->admin);

        $this->ticketNoteRepository->save($ticketNote);

        $this->eventBus->dispatch(new TicketRejectedEvent($ticket, $approve, $rejectTicket->note));
    }
}

