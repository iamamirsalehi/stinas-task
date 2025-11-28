<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\ExternalServiceCallLog;

interface ExternalServiceCallLogRepository
{
    public function save(ExternalServiceCallLog $log): void;

    /**
     * Get all failed logs that need retry
     *
     * @return array<ExternalServiceCallLog>
     */
    public function getFailedLogsForRetry(): array;
}

