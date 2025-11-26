<?php

namespace App\Providers;

use App\Infrastructure\Persist\Repository\AdminRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentAdminRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentTicketRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentUserRepository;
use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Infrastructure\Persist\Repository\UserRepository;
use App\Models\Admin;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, function ($app){
            return new EloquentUserRepository(new User());
        });

        $this->app->bind(AdminRepository::class, function ($app){
            return new EloquentAdminRepository(new Admin());
        });

        $this->app->bind(TicketRepository::class, function ($app){
            return new EloquentTicketRepository(new Ticket());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
