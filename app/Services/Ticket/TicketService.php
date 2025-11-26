<?php

namespace App\Services\Ticket;

use App\Exception\TicketException;
use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Models\Ticket;
use App\Services\Attachment\AttachmentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private AttachmentService $attachmentService)
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

    public function list(int $perPage, int $page): LengthAwarePaginator
    {        
        if ($perPage > 30 || $perPage < 1) {
            throw TicketException::invalidPerPage();
        }

        return $this->ticketRepository->list($perPage, $page);
    }
}