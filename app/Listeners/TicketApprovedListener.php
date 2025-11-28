<?php

namespace App\Listeners;

use App\Events\TicketApprovedEvent;
use App\Events\TicketFinalApprovedEvent;
use App\Infrastructure\Bus\EventBus;
use App\Mail\TicketApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class TicketApprovedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(private EventBus $eventBus)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TicketApprovedEvent $event): void
    {
        $ticket = $event->ticket;

        if ($event->ticketApproveStep->isFinal()){
            $this->eventBus->dispatch(new TicketFinalApprovedEvent($ticket));
        }
        
        $user = $ticket->user;

        if ($user && $user->email) {
            Mail::to($user->email)->send(new TicketApprovedMail($ticket));
        }
    }
}
