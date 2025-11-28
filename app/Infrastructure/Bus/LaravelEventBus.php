<?php

namespace App\Infrastructure\Bus;

use Illuminate\Contracts\Events\Dispatcher as LaravelDispatcher;

class LaravelEventBus implements EventBus
{
    protected $dispatcher;

    public function __construct(LaravelDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    } 
}