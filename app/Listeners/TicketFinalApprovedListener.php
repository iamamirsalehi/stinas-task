<?php

namespace App\Listeners;

use App\Events\TicketFinalApprovedEvent;
use App\Infrastructure\Persist\Repository\ExternalServiceCallLogRepository;
use App\Jobs\RetryExternalServiceCall;
use App\Models\ExternalServiceCallLog;
use App\Services\ExternalService\ExternalServiceAdapter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TicketFinalApprovedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        private ExternalServiceAdapter $externalServiceAdapter,
        private ExternalServiceCallLogRepository $logRepository
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(TicketFinalApprovedEvent $event): void
    {
        $ticket = $event->ticket;

        try {
            $success = $this->externalServiceAdapter->sendTicket($ticket);

            $log = ExternalServiceCallLog::new(
                ticketId: $ticket->id,
                success: $success,
                errorMessage: $success ? null : 'External service call failed',
                retryCount: 0
            );

            $this->logRepository->save($log);

            if (!$success) {
                // Schedule retry job for 1 hour later
                RetryExternalServiceCall::dispatch($log->id)->delay(now()->addHour());
                Log::info("Scheduled retry for external service call for ticket ID: {$ticket->id}");
            } else {
                Log::info("External service call succeeded for ticket ID: {$ticket->id}");
            }
        } catch (\Exception $e) {
            $log = ExternalServiceCallLog::new(
                ticketId: $ticket->id,
                success: false,
                errorMessage: $e->getMessage(),
                retryCount: 0
            );

            $this->logRepository->save($log);

            // Schedule retry job for 1 hour later
            RetryExternalServiceCall::dispatch($log->id)->delay(now()->addHour());
            Log::error("Exception during external service call for ticket ID: {$ticket->id}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}

