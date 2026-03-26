<?php

use App\Providers\AppServiceProvider;
use Modules\BibleLearning\Providers\BibleLearningServiceProvider;
use Modules\PptLivestream\Providers\PptLivestreamServiceProvider;

return [
    AppServiceProvider::class,
    PptLivestreamServiceProvider::class,
    BibleLearningServiceProvider::class,
];
