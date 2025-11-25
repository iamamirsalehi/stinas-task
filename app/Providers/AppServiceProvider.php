<?php

namespace App\Providers;

use App\Services\Attachment\AttachmentService;
use App\Services\Attachment\LocalAttachmentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AttachmentService::class, LocalAttachmentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
