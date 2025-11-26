<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketApproveStep extends Model
{
    protected $fillable = [
        'admin_id',
        'status',
        'is_final',
        'order',
    ];

    public function isFinal(): bool
    {
        return $this->is_final == true;
    }
}

