<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\TicketApproveStep;

interface TicketApproveStepRepository
{
    public function getByOrder(int $order): TicketApproveStep;

    public function findByOrder(int $order): ?TicketApproveStep;
}

