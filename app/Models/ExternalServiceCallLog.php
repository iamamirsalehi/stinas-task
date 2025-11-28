<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalServiceCallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'success',
        'error_message',
        'retry_count',
        'next_retry_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'retry_count' => 'integer',
        'next_retry_at' => 'datetime',
    ];

    public static function new(
        int $ticketId,
        bool $success = false,
        ?string $errorMessage = null,
        int $retryCount = 0
    ): self {
        $self = new self();
        $self->ticket_id = $ticketId;
        $self->success = $success;
        $self->error_message = $errorMessage;
        $self->retry_count = $retryCount;
        
        if (!$success) {
            $self->next_retry_at = now()->addHour();
        }
        
        return $self;
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function markAsSuccess(): void
    {
        $this->success = true;
        $this->error_message = null;
        $this->next_retry_at = null;
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->success = false;
        $this->error_message = $errorMessage;
        $this->retry_count++;
        $this->next_retry_at = now()->addHour();
    }
}

