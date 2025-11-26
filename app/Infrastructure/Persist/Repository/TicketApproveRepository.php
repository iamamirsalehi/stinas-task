<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\TicketApprove;

interface TicketApproveRepository
{
    public function getByOrder(int $order): TicketApprove;

    public function findByOrder(int $order): ?TicketApprove;
}