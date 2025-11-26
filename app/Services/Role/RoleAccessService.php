<?php

namespace App\Services\Role;

use App\Enums\Admin as EnumsAdmin;
use App\Enums\TicketStatus;
use App\Exception\AdminException;
use App\Models\Admin;

class RoleAccessService
{
    public function __construct()
    {}

    public function getTasksStatuses(Admin $admin): array
    {
        switch ($admin->username){
            case EnumsAdmin::Admin1->value:
                return [TicketStatus::Submitted];
            case EnumsAdmin::Admin2->value:
                return [TicketStatus::ApprovedByAdmin1];
            default:
                throw AdminException::invalidAdmin();
        }
    }
}