<?php

namespace App\Services\Ticket;

use App\Enums\TicketStatus;
use App\Events\TicketApprovedEvent;
use App\Events\TicketRejectedEvent;
use App\Exception\TicketException;
use App\Infrastructure\Bus\EventBus;
use App\Infrastructure\Persist\Repository\TicketNoteRepository;
use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Models\Ticket;
use App\Models\TicketNote;
use App\Services\Attachment\AttachmentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private TicketNoteRepository $ticketNoteRepository,
        private TicketApproveService $ticketApproveService,
        private AttachmentService $attachmentService,
        private EventBus $eventBus,
    )
    {}

    public function add(AddNewTicket $addNewTicket): void
    {
        $storedFile = $this->attachmentService->store(
            $addNewTicket->uploadedFile->path(),
            $addNewTicket->uploadedFile->getClientOriginalExtension()
        );

        $ticket = Ticket::new(
            $addNewTicket->title,
            $addNewTicket->description,
            $storedFile->path,
            $addNewTicket->user,
        );

        $this->ticketRepository->save($ticket);
    }

    public function approve(ApproveTicket $approveTicket): void
    {
        $ticket = $this->ticketRepository->getByID($approveTicket->ticketID);

        $approve = $this->ticketApproveService->getApprove($ticket);

        if (is_null($approve)){
            throw TicketException::canNotHaveActionOnTicket();
        }

        $ticket->approve(TicketStatus::from($approve->status));

        $this->ticketRepository->save($ticket);

        $ticketNote = TicketNote::new($approveTicket->note, $ticket, $approveTicket->admin);

        $this->ticketNoteRepository->save($ticketNote);

        $this->eventBus->dispatch(new TicketApprovedEvent($ticket, $approve));
    }

    public function reject(RejectTicket $rejectTicket): void
    {
        $ticket = $this->ticketRepository->getByID($rejectTicket->ticketID);

        $approve = $this->ticketApproveService->getApprove($ticket);

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

    public function list(int $perPage, int $page, array $statuses = []): LengthAwarePaginator
    {        
        if ($perPage > 30 || $perPage < 1) {
            throw TicketException::invalidPerPage();
        }

        return $this->ticketRepository->list($perPage, $page, $statuses);
    }

    public function getByID(int $id): Ticket
    {
        $ticket = $this->ticketRepository->getByID($id);

        if (!$ticket) {
            throw TicketException::invalidID();
        }

        return $ticket;
    }
}