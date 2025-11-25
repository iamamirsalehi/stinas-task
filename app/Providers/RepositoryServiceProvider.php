<?php

namespace App\Providers;

use App\Infrastructure\Persist\Repository\Eloquent\EloquentUserRepository;
use App\Infrastructure\Persist\Repository\UserRepository;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
