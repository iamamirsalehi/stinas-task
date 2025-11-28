<?php

namespace App\Jobs;

use App\Infrastructure\Persist\Repository\ExternalServiceCallLogRepository;
use App\Models\ExternalServiceCallLog;
use App\Services\ExternalService\ExternalServiceAdapter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RetryExternalServiceCall implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $logId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        ExternalServiceAdapter $externalServiceAdapter,
        ExternalServiceCallLogRepository $logRepository
    ): void {
        $log = ExternalServiceCallLog::find($this->logId);

        if (!$log) {
            Log::warning("External service call log not found: {$this->logId}");
            return;
        }

        // If already succeeded, no need to retry
        if ($log->success) {
            Log::info("External service call already succeeded for log ID: {$this->logId}");
            return;
        }

        $ticket = $log->ticket;

        if (!$ticket) {
            Log::warning("Ticket not found for log ID: {$this->logId}");
            return;
        }

        try {
            $success = $externalServiceAdapter->sendTicket($ticket);

            if ($success) {
                $log->markAsSuccess();
                $logRepository->save($log);
                Log::info("External service call succeeded on retry for ticket ID: {$ticket->id}");
            } else {
                $log->markAsFailed('External service call failed on retry');
                $logRepository->save($log);
                
                // Schedule another retry for 1 hour later
                RetryExternalServiceCall::dispatch($log->id)->delay(now()->addHour());
                Log::info("Scheduled another retry for external service call for ticket ID: {$ticket->id}");
            }
        } catch (\Exception $e) {
            $log->markAsFailed($e->getMessage());
            $logRepository->save($log);
            
            // Schedule another retry for 1 hour later
            RetryExternalServiceCall::dispatch($log->id)->delay(now()->addHour());
            Log::error("Exception during retry for external service call for ticket ID: {$ticket->id}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}
