<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use App\Exception\AdminException;
use App\Infrastructure\Persist\Repository\AdminRepository;
use App\Models\Admin;

class EloquentAdminRepository extends EloquentBaseRepository implements AdminRepository
{
    public function getByID(int $id): Admin
    {
        $admin =  $this->model->query()->where("id", $id)->first();

        if (is_null($admin)) {
            throw AdminException::adminDoesNotExist();
        }

        return $admin;
    }
}