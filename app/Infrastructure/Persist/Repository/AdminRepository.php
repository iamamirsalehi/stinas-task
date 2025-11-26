<?php

namespace App\Infrastructure\Persist\Repository;

use App\Models\Admin;

interface AdminRepository
{
    public function getByID(int $id): Admin;

    public function getByUserName(string $username): Admin;
}