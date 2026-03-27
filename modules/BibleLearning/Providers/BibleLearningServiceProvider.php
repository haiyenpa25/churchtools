<?php

namespace Modules\BibleLearning\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\BibleLearning\Contracts\ApprovalRepositoryInterface;
use Modules\BibleLearning\Contracts\BibleLearningExtractorContract;
use Modules\BibleLearning\Contracts\FlashcardRepositoryInterface;
use Modules\BibleLearning\Repositories\ApprovalRepository;
use Modules\BibleLearning\Repositories\FlashcardRepository;
use Modules\BibleLearning\Services\GeminiExtractionService;

class BibleLearningServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BibleLearningExtractorContract::class, GeminiExtractionService::class);
        $this->app->bind(ApprovalRepositoryInterface::class, ApprovalRepository::class);
        $this->app->bind(
            \Modules\BibleLearning\Contracts\FlashcardRepositoryInterface::class,
            \Modules\BibleLearning\Repositories\FlashcardRepository::class
        );
        $this->app->bind(
            \Modules\BibleLearning\Contracts\EventRepositoryInterface::class,
            \Modules\BibleLearning\Repositories\EventRepository::class
        );
        $this->app->bind(
            \Modules\BibleLearning\Contracts\QuizRepositoryInterface::class,
            \Modules\BibleLearning\Repositories\QuizRepository::class
        );
        $this->app->bind(
            \Modules\BibleLearning\Contracts\GraphRepositoryInterface::class,
            \Modules\BibleLearning\Repositories\GraphRepository::class
        );
        $this->app->bind(
            \Modules\BibleLearning\Contracts\ImportTrackerRepositoryInterface::class,
            \Modules\BibleLearning\Repositories\ImportTrackerRepository::class
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'biblelearning');
    }
}
