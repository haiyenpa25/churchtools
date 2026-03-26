<?php

namespace Modules\PptLivestream\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\PptLivestream\Contracts\PptEngineServiceInterface;
use Modules\PptLivestream\Contracts\TemplateRepositoryInterface;
use Modules\PptLivestream\Repositories\TemplateRepository;
use Modules\PptLivestream\Services\PptEngineService;

class PptLivestreamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PptEngineServiceInterface::class, PptEngineService::class);
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
    }

    public function boot(): void
    {
        // Configure module routes or views if needed
    }
}
