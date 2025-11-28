<?php

namespace App\Providers;

use App\Infrastructure\Bus\EventBus;
use App\Infrastructure\Bus\LaravelEventBus;
use App\Services\Attachment\AttachmentDownloadable;
use App\Services\Attachment\AttachmentService;
use App\Services\Attachment\LocalAttachmentService;
use App\Services\ExternalService\ExternalServiceAdapter;
use App\Services\ExternalService\FakeExternalServiceAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventBus::class, LaravelEventBus::class);

        $this->app->bind(AttachmentService::class, LocalAttachmentService::class);

        $this->app->bind(AttachmentDownloadable::class, LocalAttachmentService::class);

        $this->app->bind(ExternalServiceAdapter::class, FakeExternalServiceAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
