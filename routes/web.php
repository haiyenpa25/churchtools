<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;
use App\Models\Song;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Modules\PptLivestream\Http\Controllers\PptController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protect all PPT Livestream routes under Auth Middleware
Route::middleware(['auth'])->group(function () {
    Route::post('/ppt/generate', [PptController::class, 'generate'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::post('/ppt/bulk-generate', [PptController::class, 'bulkGenerate'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::post('/api/ppt/extract', [PptController::class, 'extractText'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::post('/api/ppt/parse', [PptController::class, 'parseText'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::get('/ppt/download/{filename}', [PptController::class, 'download'])->withoutMiddleware([ValidateCsrfToken::class]);

    Route::get('/ppt/layout-editor', function () {
        $schemaPath = base_path('engine/layout_schema.json');
        $schema = json_decode(file_get_contents($schemaPath), true);
        return view('ppt_layout_editor', ['schema' => $schema]);
    });

    Route::post('/api/ppt/save-layout', function (Request $request) {
        $data = $request->validate(['schema' => 'required|array']);
        $schemaPath = base_path('engine/layout_schema.json');
        file_put_contents($schemaPath, json_encode($data['schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return response()->json(['status' => 'saved']);
    })->withoutMiddleware([ValidateCsrfToken::class]);

    Route::get('/api/ppt/layout-schema', function () {
        $schemaPath = base_path('engine/layout_schema.json');
        $schema = json_decode(file_get_contents($schemaPath), true);
        return response()->json($schema);
    });

    Route::get('/ppt', function () {
        return view('ppt.index');
    });

    Route::get('/ppt/sermon', function () {
        return view('ppt.sermon');
    });

    Route::get('/api/ppt/templates', [PptController::class, 'getTemplates']);
    Route::get('/api/ppt/templates/{id}', [PptController::class, 'getTemplate']);

    Route::post('/api/ppt/templates', [PptController::class, 'storeTemplate'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::post('/api/ppt/templates/{id}', [PptController::class, 'updateTemplate'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::delete('/api/ppt/templates/{id}', [PptController::class, 'destroyTemplate'])->withoutMiddleware([ValidateCsrfToken::class]);

    Route::get('/ppt/templates', function () {
        return view('ppt.templates');
    });

    Route::post('/api/ppt/sermon/parse', [PptController::class, 'sermonParsePdf'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::post('/api/ppt/sermon/parse-text', [PptController::class, 'sermonParseText'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::post('/api/ppt/sermon/analyze-template', [PptController::class, 'sermonAnalyzeTemplate'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::post('/api/ppt/sermon/generate', [PptController::class, 'sermonGenerate'])->withoutMiddleware([ValidateCsrfToken::class]);
    Route::get('/ppt/sermon/download/{filename}', [PptController::class, 'sermonDownload']);

    // FinanceTracker PWA routes
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
    Route::get('/api/finance/overview', [\App\Http\Controllers\FinanceController::class, 'getOverview'])->name('finance.overview');
    Route::post('/api/finance/accounts', [\App\Http\Controllers\FinanceController::class, 'storeAccount'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.accounts.store');
    Route::put('/api/finance/accounts/{id}', [\App\Http\Controllers\FinanceController::class, 'updateAccount'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.accounts.update');
    Route::delete('/api/finance/accounts/{id}', [\App\Http\Controllers\FinanceController::class, 'deleteAccount'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.accounts.delete');
    Route::post('/api/finance/transactions', [\App\Http\Controllers\FinanceController::class, 'storeTransaction'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.transactions.store');
    Route::delete('/api/finance/transactions/{id}', [\App\Http\Controllers\FinanceController::class, 'deleteTransaction'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.transactions.delete');
    Route::post('/api/finance/debts', [\App\Http\Controllers\FinanceController::class, 'storeDebt'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.debts.store');
    Route::post('/api/finance/debts/{id}/toggle', [\App\Http\Controllers\FinanceController::class, 'toggleDebtStatus'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.debts.toggle');
    Route::delete('/api/finance/debts/{id}', [\App\Http\Controllers\FinanceController::class, 'deleteDebt'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.debts.delete');
    Route::post('/api/finance/investments', [\App\Http\Controllers\FinanceController::class, 'storeInvestment'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.investments.store');
    Route::put('/api/finance/investments/{id}', [\App\Http\Controllers\FinanceController::class, 'updateInvestment'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.investments.update');
    Route::delete('/api/finance/investments/{id}', [\App\Http\Controllers\FinanceController::class, 'deleteInvestment'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.investments.delete');
    Route::post('/api/finance/rates/update', [\App\Http\Controllers\FinanceController::class, 'updateRates'])->withoutMiddleware([ValidateCsrfToken::class])->name('finance.rates.update');
});

// Web Backdoor Auto Deploy Setup (Bypasses CLI php requirements on hosting - completely public to allow first-time migrations & seeding)
Route::get('/api/finance/admin/deploy-setup', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = "Migrate Output:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n\n";

        // Seed the entire DatabaseSeeder (creates haiyenpa25 user & calls FinanceSeeder)
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $output .= "DatabaseSeeder Output:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n\n";

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $output .= "Optimize Clear Output:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n\n";

        return "<pre>🚀 TRIỂN KHAI FINANCE PWA THÀNH CÔNG:\n\n" . $output . "</pre>";
    } catch (\Exception $e) {
        return "<pre>❌ LỖI TRIỂN KHAI:\n\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    }
});

// Song Library routes (keep open or protect as needed, keeping open for now to match old behavior)
Route::get('/api/songs', function (Request $request) {
    return Song::orderByRaw('CAST(number AS UNSIGNED) ASC, title ASC')->get();
});
Route::get('/songs', [SongController::class, 'index']);
$noCsrf = [ValidateCsrfToken::class];
Route::post('/api/songs', [SongController::class, 'store'])->withoutMiddleware($noCsrf);
Route::put('/api/songs/{id}', [SongController::class, 'update'])->withoutMiddleware($noCsrf);
Route::delete('/api/songs/{id}', [SongController::class, 'destroy'])->withoutMiddleware($noCsrf);

// BibleFlow AI (Kinh Thánh Karaoke)
Route::get('/bibleflow', function () {
    return view('bibleflow.index');
});
