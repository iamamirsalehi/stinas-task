<?php

namespace App\Providers;

use App\Infrastructure\Persist\Repository\AdminRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentAdminRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentTicketApproveStepRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentTicketNoteRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentTicketRepository;
use App\Infrastructure\Persist\Repository\Eloquent\EloquentUserRepository;
use App\Infrastructure\Persist\Repository\TicketApproveStepRepository;
use App\Infrastructure\Persist\Repository\TicketNoteRepository;
use App\Infrastructure\Persist\Repository\TicketRepository;
use App\Infrastructure\Persist\Repository\UserRepository;
use App\Models\Admin;
use App\Models\Ticket;
use App\Models\TicketApproveStep;
use App\Models\TicketNote;
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

        $this->app->bind(TicketApproveStepRepository::class, function ($app){
            return new EloquentTicketApproveStepRepository(new TicketApproveStep());
        });

        $this->app->bind(TicketNoteRepository::class, function ($app){
            return new EloquentTicketNoteRepository(new TicketNote());
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
