<?php

namespace App\Listeners;

use App\Events\TicketRejectedEvent;
use App\Mail\TicketRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class TicketRejectedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketRejectedEvent $event): void
    {
        $ticket = $event->ticket;
        $user = $ticket->user;

        if ($user && $user->email) {
            Mail::to($user->email)->send(new TicketRejectedMail($ticket, $event->note));
        }
    }
}

