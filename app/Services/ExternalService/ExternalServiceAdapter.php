<?php

namespace App\Services\ExternalService;

use App\Models\Ticket;

interface ExternalServiceAdapter
{
    /**
     * Send ticket data to external service
     *
     * @param Ticket $ticket
     * @return bool Returns true on success, false on failure
     */
    public function sendTicket(Ticket $ticket): bool;
}

