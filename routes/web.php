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

// Web Backdoor Auto Deploy Setup
Route::get('/api/finance/admin/deploy-setup', function () {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(120);

    $output = '';
    $errors = '';

    // Step 1: Migrate
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output .= "✅ MIGRATE:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n";
    } catch (\Throwable $e) {
        $errors .= "❌ MIGRATE LỖI: " . $e->getMessage() . "\n\n";
    }

    // Step 2: Seed user haiyenpa25 only (không dùng DatabaseSeeder để tránh lỗi PptTemplateSeeder)
    try {
        // Tạo user haiyenpa25 thủ công nếu chưa có
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'haiyenpa25'],
            ['name' => 'Hải Yến', 'password' => bcrypt('Haiyen@2026')]
        );
        $output .= "✅ USER: Tài khoản haiyenpa25 đã sẵn sàng (ID: {$user->id})\n\n";
    } catch (\Throwable $e) {
        $errors .= "❌ USER LỖI: " . $e->getMessage() . "\n\n";
    }

    // Step 3: Seed Finance Data
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'FinanceSeeder', '--force' => true]);
        $output .= "✅ FINANCE SEEDER:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n";
    } catch (\Throwable $e) {
        $errors .= "❌ FINANCE SEEDER LỖI: " . $e->getMessage() . "\n\n";
    }

    // Step 4: Clear cache
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $output .= "✅ CACHE CLEAR:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n";
    } catch (\Throwable $e) {
        // optimize:clear không quan trọng, bỏ qua lỗi
        $output .= "⚠️ Cache clear skipped.\n";
    }

    $status = empty($errors) ? "🚀 TRIỂN KHAI THÀNH CÔNG" : "⚠️ TRIỂN KHAI XONG (có một số lỗi)";

    return response("<pre style='background:#0b0f19;color:#f1f5f9;padding:2rem;font-size:13px;line-height:1.6'>"
        . "<b style='color:#f59e0b;font-size:16px'>{$status}</b>\n\n"
        . ($errors ? "<b style='color:#f43f5e'>LỖI:</b>\n{$errors}" : "")
        . "<b style='color:#10b981'>KẾT QUẢ:</b>\n{$output}"
        . "\n<hr style='border-color:#333'>\n<a href='https://hyb.io.vn/login' style='color:#f59e0b'>→ Đăng nhập ngay</a></pre>", 200)
        ->header('Content-Type', 'text/html; charset=utf-8');
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
