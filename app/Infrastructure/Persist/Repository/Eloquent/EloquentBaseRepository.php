<?php

namespace App\Infrastructure\Persist\Repository\Eloquent;

use Illuminate\Database\Eloquent\Model;

class EloquentBaseRepository
{
    public function __construct(protected Model $model)
    {
    }
}