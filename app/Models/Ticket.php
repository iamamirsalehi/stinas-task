<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'status',
        'file_path',
    ];

    public function casts(): array
    {
        return [
            'status' => TicketStatus::class,
        ];
    }

    public static function new(
        string $title,
        string $description,
        string $filePath,
        User $user,
    ): self
    {
        $self = new self();

        $self->title = $title;
        $self->description = $description;
        $self->file_path = $filePath;
        $self->status = TicketStatus::Submitted;
        $self->user_id = $user->id;

        return $self;
    }
}
