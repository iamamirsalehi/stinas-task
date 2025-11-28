<?php

namespace App\Infrastructure\Bus;

interface EventBus
{
    public function dispatch(object $event): void;
}