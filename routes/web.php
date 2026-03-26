<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\SongController;
use App\Models\Song;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Modules\PptLivestream\Http\Controllers\PptController;

// Using web route for quick testing (bypassing auth), disable CSRF in Bootstrap later if needed
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

// GET current schema — used by WYSIWYG preview to always get the latest layout
Route::get('/api/ppt/layout-schema', function () {
    $schemaPath = base_path('engine/layout_schema.json');
    $schema = json_decode(file_get_contents($schemaPath), true);

    return response()->json($schema);
});

// Clean URL alias — uses new split view structure
Route::get('/ppt', function () {
    return view('ppt.index');
});
Route::get('/ppt/sermon', function () {
    return view('ppt.sermon');
});

Route::get('/api/ppt/templates', [PptController::class, 'getTemplates']);
Route::get('/api/ppt/templates/{id}', [PptController::class, 'getTemplate']);

// ── GET all songs from Library ──────────────────────────────────────────
Route::get('/api/songs', function (Request $request) {
    return Song::orderByRaw('CAST(number AS UNSIGNED) ASC, title ASC')->get();
});

Route::get('/songs', [SongController::class, 'index']);
$noCsrf = [ValidateCsrfToken::class];
Route::post('/api/songs', [SongController::class, 'store'])->withoutMiddleware($noCsrf);
Route::put('/api/songs/{id}', [SongController::class, 'update'])->withoutMiddleware($noCsrf);
Route::delete('/api/songs/{id}', [SongController::class, 'destroy'])->withoutMiddleware($noCsrf);

// Template CRUD (without CSRF — UI handles its own state)
$noCsrf = [ValidateCsrfToken::class];
Route::post('/api/ppt/templates', [PptController::class, 'storeTemplate'])->withoutMiddleware($noCsrf);
Route::post('/api/ppt/templates/{id}', [PptController::class, 'updateTemplate'])->withoutMiddleware($noCsrf);
Route::delete('/api/ppt/templates/{id}', [PptController::class, 'destroyTemplate'])->withoutMiddleware($noCsrf);

// Template manager page
Route::get('/ppt/templates', function () {
    return view('ppt.templates');
});

// ── Sermon Live Lower-Third ────────────────────────────────────────────────
Route::get('/ppt/sermon', function () {
    return view('ppt.sermon');
});
Route::post('/api/ppt/sermon/parse', [PptController::class, 'sermonParsePdf'])->withoutMiddleware($noCsrf ?? [ValidateCsrfToken::class]);
Route::post('/api/ppt/sermon/parse-text', [PptController::class, 'sermonParseText'])->withoutMiddleware([ValidateCsrfToken::class]);
Route::post('/api/ppt/sermon/analyze-template', [PptController::class, 'sermonAnalyzeTemplate'])->withoutMiddleware([ValidateCsrfToken::class]);
Route::post('/api/ppt/sermon/generate', [PptController::class, 'sermonGenerate'])->withoutMiddleware([ValidateCsrfToken::class]);
Route::get('/ppt/sermon/download/{filename}', [PptController::class, 'sermonDownload']);

// ── BibleFlow AI (Kinh Thánh Karaoke) ──────────────────────────────────────
Route::get('/bibleflow', function () {
    return view('bibleflow.index');
});
