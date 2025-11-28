<?php

namespace App\Services\ExternalService;

use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class FakeExternalServiceAdapter implements ExternalServiceAdapter
{
    /**
     * Send ticket data to external service
     * This is a fake implementation that simulates external service calls
     *
     * @param Ticket $ticket
     * @return bool Returns true on success, false on failure
     */
    public function sendTicket(Ticket $ticket): bool
    {
        // Simulate external service call
        // In a real scenario, this would make an HTTP request to an external API
        
        // For demonstration, we'll randomly fail 30% of the time to test retry logic
        // In production, this would be replaced with actual API call
        $shouldFail = rand(1, 100) <= 30;
        
        if ($shouldFail) {
            Log::info('Fake external service call failed for ticket ID: ' . $ticket->id);
            return false;
        }
        
        Log::info('Fake external service call succeeded for ticket ID: ' . $ticket->id);
        return true;
    }
}

