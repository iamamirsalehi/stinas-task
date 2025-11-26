<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketNote extends Model
{
    protected $fillable = [
        'note',
        'ticket_id',
        'admin_id',
    ];

    public static function new(
        string $note,
        Ticket $ticket,
        Admin $admin,
    ): self
    {
        $self = new self();

        $self->note = $note;
        $self->ticket_id = $ticket->id;
        $self->admin_id = $admin->id;

        return $self;
    }
}
