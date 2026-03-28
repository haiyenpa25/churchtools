<?php

namespace Modules\BibleLearning\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\BibleLearning\Contracts\ApprovalRepositoryInterface;
use Modules\BibleLearning\Contracts\BibleLearningExtractorContract;
use Modules\BibleLearning\Contracts\BibleManagerRepositoryInterface;
use Modules\BibleLearning\Contracts\EventRepositoryInterface;
use Modules\BibleLearning\Contracts\FlashcardRepositoryInterface;
use Modules\BibleLearning\Contracts\GraphRepositoryInterface;
use Modules\BibleLearning\Contracts\ImportTrackerRepositoryInterface;
use Modules\BibleLearning\Contracts\QuizRepositoryInterface;
use Modules\BibleLearning\Repositories\ApprovalRepository;
use Modules\BibleLearning\Repositories\BibleManagerRepository;
use Modules\BibleLearning\Repositories\EventRepository;
use Modules\BibleLearning\Repositories\FlashcardRepository;
use Modules\BibleLearning\Repositories\GraphRepository;
use Modules\BibleLearning\Repositories\ImportTrackerRepository;
use Modules\BibleLearning\Repositories\QuizRepository;
use Modules\BibleLearning\Services\GeminiExtractionService;

class BibleLearningServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BibleLearningExtractorContract::class, GeminiExtractionService::class);
        $this->app->bind(ApprovalRepositoryInterface::class, ApprovalRepository::class);
        $this->app->bind(
            FlashcardRepositoryInterface::class,
            FlashcardRepository::class
        );
        $this->app->bind(
            EventRepositoryInterface::class,
            EventRepository::class
        );
        $this->app->bind(
            QuizRepositoryInterface::class,
            QuizRepository::class
        );
        $this->app->bind(
            GraphRepositoryInterface::class,
            GraphRepository::class
        );
        $this->app->bind(
            ImportTrackerRepositoryInterface::class,
            ImportTrackerRepository::class
        );
        $this->app->bind(
            BibleManagerRepositoryInterface::class,
            BibleManagerRepository::class
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'biblelearning');
    }
}
