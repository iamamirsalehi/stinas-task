<?php

namespace App\Services\Ticket;

use App\Enums\TicketStatus;
use App\Events\TicketApprovedEvent;
use App\Exception\TicketException;
use App\Infrastructure\Bus\EventBus;
use App\Infrastructure\Persist\Repository\TicketNoteRepository;
use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Models\TicketNote;

class TicketApproveService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private TicketNoteRepository $ticketNoteRepository,
        private TicketApproveStepService $ticketApproveStepService,
        private EventBus $eventBus,
    )
    {}

    public function approve(ApproveTicket $approveTicket): void
    {
        $ticket = $this->ticketRepository->getByID($approveTicket->ticketID);

        $approve = $this->ticketApproveStepService->getApprove($ticket);

        if (is_null($approve)){
            throw TicketException::canNotHaveActionOnTicket();
        }

        $ticket->approve(TicketStatus::from($approve->status));

        $this->ticketRepository->save($ticket);

        $ticketNote = TicketNote::new($approveTicket->note, $ticket, $approveTicket->admin);

        $this->ticketNoteRepository->save($ticketNote);

        $this->eventBus->dispatch(new TicketApprovedEvent($ticket, $approve));
    }

    public function approveBulk(array $ticketIDs, \App\Models\Admin $admin, string $note = 'Bulk action'): void
    {
        foreach ($ticketIDs as $ticketID) {
            $approveTicket = new ApproveTicket(
                (int) $ticketID,
                $note,
                $admin
            );
            $this->approve($approveTicket);
        }
    }
}

