<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Infrastructure\Persist\Repository\ExternalServiceCallLogRepository;
use App\Models\ExternalServiceCallLog;

class EloquentExternalServiceCallLogRepository extends EloquentBaseRepository implements ExternalServiceCallLogRepository
{
    public function save(ExternalServiceCallLog $log): void
    {
        $log->save();
    }

    /**
     * Get all failed logs that need retry
     *
     * @return array<ExternalServiceCallLog>
     */
    public function getFailedLogsForRetry(): array
    {
        return $this->model->query()
            ->where('success', false)
            ->where('next_retry_at', '<=', now())
            ->with('ticket')
            ->get()
            ->all();
    }
}

