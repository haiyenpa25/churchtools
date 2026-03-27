<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Modules\BibleLearning\Http\Controllers\ApprovalController;
use Modules\BibleLearning\Http\Controllers\EventController;
use Modules\BibleLearning\Http\Controllers\FlashcardController;
use Modules\BibleLearning\Http\Controllers\GraphController;
use Modules\BibleLearning\Http\Controllers\PortalController;
use Modules\BibleLearning\Http\Controllers\QuizController;

Route::middleware([])->group(function () {
    // PORTAL HUB
    Route::get('/bible-learning', [PortalController::class, 'index'])->name('biblelearning.portal');
    Route::get('/api/portal/stats', [PortalController::class, 'getStats']);

    // EVENT TIMELINE
    Route::get('/bible-learning/timeline', [EventController::class, 'index'])->name('biblelearning.timeline');
    Route::get('/api/events', [EventController::class, 'getEvents']);

    // QUIZ ARENA (MỚI BỔ SUNG)
    Route::get('/bible-learning/quiz', [QuizController::class, 'index'])->name('biblelearning.quiz');
    Route::get('/api/quizzes/random', [QuizController::class, 'fetchQuizSession']);

    // KNOWLEDGE GRAPH
    Route::get('/bible-learning/graph', [GraphController::class, 'index'])->name('biblelearning.graph');
    Route::get('/api/graph', [GraphController::class, 'fetchGraph']);
    Route::get('/api/graph/neighbors/{nodeId}', [GraphController::class, 'fetchNeighbors']);
    Route::post('/api/graph/parse-text', [GraphController::class, 'parseText']);

    // KNOWLEDGE GRAPH ADMIN COMMANDS
    Route::post('/api/graph/admin/reset', [GraphController::class, 'adminReset']);
    Route::post('/api/graph/admin/export', [GraphController::class, 'adminExport']);
    Route::post('/api/graph/admin/import', [GraphController::class, 'adminImport']);
    Route::post('/api/graph/admin/ingest', [GraphController::class, 'adminIngest']);

    // Quản Lý Tracking Nạp Dữ Liệu
    Route::get('/api/graph/admin/ingestion-status', [GraphController::class, 'adminGetIngestionStatus']);
    Route::post('/api/graph/admin/ingest-single', [GraphController::class, 'adminIngestSingleFile']);
    Route::post('/api/graph/admin/work-queue', [GraphController::class, 'adminWorkQueue']);

    // BIBLE TEXT & COMMENTARY (Local files)
    Route::get('/api/bible/text', [GraphController::class, 'getBibleText']);
    Route::get('/api/bible/commentary', [GraphController::class, 'getBibleCommentary']);
    Route::get('/api/bible/import-guide', [GraphController::class, 'getBibleImportGuide']);
    Route::get('/api/bible/commentary-books', [GraphController::class, 'getCommentaryBooks']);

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

Route::get('/api/graph/admin/force-migrate', function () {
    Artisan::call('migrate', ['--force' => true]);

    return '✅ MIGRATION THÀNH CÔNG, CSDL ĐÃ ĐƯỢC TẠO!';
});
