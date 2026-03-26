<?php

use Illuminate\Support\Facades\Route;
use Modules\BibleLearning\Http\Controllers\ApprovalController;
use Modules\BibleLearning\Http\Controllers\FlashcardController;

Route::middleware([])->group(function () {
    // PORTAL HUB
    Route::get('/bible-learning', [\Modules\BibleLearning\Http\Controllers\PortalController::class, 'index'])->name('biblelearning.portal');
    Route::get('/api/portal/stats', [\Modules\BibleLearning\Http\Controllers\PortalController::class, 'getStats']);

    // EVENT TIMELINE
    Route::get('/bible-learning/timeline', [\Modules\BibleLearning\Http\Controllers\EventController::class, 'index'])->name('biblelearning.timeline');
    Route::get('/api/events', [\Modules\BibleLearning\Http\Controllers\EventController::class, 'getEvents']);

    // QUIZ ARENA (MỚI BỔ SUNG)
    Route::get('/bible-learning/quiz', [\Modules\BibleLearning\Http\Controllers\QuizController::class, 'index'])->name('biblelearning.quiz');
    Route::get('/api/quizzes/random', [\Modules\BibleLearning\Http\Controllers\QuizController::class, 'fetchQuizSession']);

    // KNOWLEDGE GRAPH
    Route::get('/bible-learning/graph', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'index'])->name('biblelearning.graph');
    Route::get('/api/graph', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'fetchGraph']);
    Route::get('/api/graph/neighbors/{nodeId}', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'fetchNeighbors']);
    Route::post('/api/graph/parse-text', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'parseText']);

    // BIBLE TEXT & COMMENTARY (Local files)
    Route::get('/api/bible/text', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'getBibleText']);
    Route::get('/api/bible/commentary', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'getBibleCommentary']);
    Route::get('/api/bible/import-guide', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'getBibleImportGuide']);
    Route::get('/api/bible/commentary-books', [\Modules\BibleLearning\Http\Controllers\GraphController::class, 'getCommentaryBooks']);


    // APPROVAL CENTER
    Route::get('/bible-learning/approval', [ApprovalController::class, 'index'])->name('biblelearning.approval.index');
    Route::get('/bible-learning/approval/pending', [ApprovalController::class, 'fetchPending']);
    Route::post('/bible-learning/approval/{id}/approve', [ApprovalController::class, 'approve']);
    Route::post('/bible-learning/approval/{id}/reject', [ApprovalController::class, 'reject']);

    // FLASHCARD & STUDY
    Route::get('/bible-learning/study', [FlashcardController::class, 'study'])->name('biblelearning.study.index');
    Route::get('/api/flashcards/due', [FlashcardController::class, 'getDueCards']);
    Route::post('/api/flashcards/{id}/review', [FlashcardController::class, 'submitReview']);

    // RAG CRAWLER
    Route::post('/api/crawl/gemini', [FlashcardController::class, 'crawlHTTLVN']);
});
