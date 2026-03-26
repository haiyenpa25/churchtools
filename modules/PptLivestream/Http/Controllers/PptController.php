<?php

namespace Modules\PptLivestream\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\PptLivestream\Contracts\PptEngineServiceInterface;
use Modules\PptLivestream\Contracts\TemplateRepositoryInterface;
use Modules\PptLivestream\DTOs\PptGenerationRequestDTO;
use Modules\PptLivestream\Services\SmartLyricsParser;

class PptController extends Controller
{
    public function __construct(
        protected PptEngineServiceInterface $pptEngineService,
        protected TemplateRepositoryInterface $templateRepository
    ) {}

    // ── GET all templates ──────────────────────────────────────────────────
    public function getTemplates()
    {
        return response()->json($this->templateRepository->getAll());
    }

    // ── GET one template ───────────────────────────────────────────────────
    public function getTemplate(int $id)
    {
        $tmpl = $this->templateRepository->findById($id);
        abort_if(! $tmpl, 404);
        $tmpl->load('presets');

        return response()->json($tmpl);
    }

    // ── CREATE template ────────────────────────────────────────────────────
    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'logo_file' => 'nullable|image|max:5120',
        ]);

        $tmpl = $this->templateRepository->create([
            'name' => $validated['name'],
            'file_path' => '',
            'status' => 'active',
        ]);

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store("template_logos/{$tmpl->id}", 'public');
            $tmpl->update(['logo_path' => $path]);
        }

        return response()->json(['status' => 'created', 'template' => $tmpl->load('presets')]);
    }

    // ── UPDATE template (name + optional new logo) ─────────────────────────
    public function updateTemplate(Request $request, int $id)
    {
        $tmpl = $this->templateRepository->findById($id);
        abort_if(! $tmpl, 404);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:120',
            'logo_file' => 'nullable|image|max:5120',
            'remove_logo' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) {
            $tmpl->name = $validated['name'];
        }

        if ($request->boolean('remove_logo') && $tmpl->logo_path) {
            Storage::disk('public')->delete($tmpl->logo_path);
            $tmpl->logo_path = null;
        }

        if ($request->hasFile('logo_file')) {
            // Delete old logo if exists
            if ($tmpl->logo_path) {
                Storage::disk('public')->delete($tmpl->logo_path);
            }
            $path = $request->file('logo_file')->store("template_logos/{$tmpl->id}", 'public');
            $tmpl->logo_path = $path;
        }

        $tmpl->save();

        return response()->json(['status' => 'updated', 'template' => $tmpl->load('presets')]);
    }

    // ── DELETE template ────────────────────────────────────────────────────
    public function destroyTemplate(int $id)
    {
        $tmpl = $this->templateRepository->findById($id);
        abort_if(! $tmpl, 404);
        if ($tmpl->logo_path) {
            Storage::disk('public')->delete($tmpl->logo_path);
        }
        $tmpl->delete();

        return response()->json(['status' => 'deleted']);
    }

    // ── Generate single ────────────────────────────────────────────────────
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'output_path' => 'sometimes|string',
            'template_path' => 'nullable|string',
            'slides' => 'required|array',
        ]);

        $dto = PptGenerationRequestDTO::fromArray($validated);
        $this->pptEngineService->generateFromPayload($dto->toArray());

        return response()->json(['status' => 'queued']);
    }

    // ── Parse raw text → blocks ────────────────────────────────────────────
    public function parseText(Request $request, SmartLyricsParser $parser)
    {
        $validated = $request->validate(['text' => 'required|string']);
        $blocks = $parser->parse($validated['text']);

        return response()->json(['status' => 'success', 'blocks' => $blocks]);
    }

    // ── Bulk generate PPTX from blocks ──────────────────────────────────────
    public function bulkGenerate(Request $request)
    {
        $blocks = is_string($request->input('blocks')) ? json_decode($request->input('blocks'), true) : $request->input('blocks');
        $overrides = is_string($request->input('overrides')) ? json_decode($request->input('overrides'), true) : $request->input('overrides');

        $request->merge(['blocks' => $blocks, 'overrides' => $overrides]);

        $validated = $request->validate([
            'blocks' => 'required|array',
            'template_id' => 'required|integer',
            'overrides' => 'nullable|array',
            'logo_file' => 'nullable|image|max:5120',
            'overrides.x' => 'nullable|numeric',
            'overrides.y' => 'nullable|numeric',
            'overrides.width' => 'nullable|numeric',
            'overrides.height' => 'nullable|numeric',
            'overrides.font_size' => 'nullable|numeric',
            'overrides.font_color' => 'nullable|string',
            'banner_color' => 'nullable|string|max:7',
            'logo_border_color' => 'nullable|string|max:7',
            'logo_bg_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
        ]);

        $overrides = $validated['overrides'] ?? [];
        $colorKeys = ['banner_color', 'logo_border_color', 'logo_bg_color', 'text_color'];
        foreach ($colorKeys as $key) {
            if ($request->filled($key)) {
                $overrides[$key] = ltrim($request->input($key), '#');
            }
        }

        // Uploaded logo takes priority; fall back to template's stored logo
        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('logos', 'public');
            $overrides['logo_path'] = storage_path('app/public/'.$path);
        } elseif (! empty($validated['template_id'])) {
            $tmpl = $this->templateRepository->findById($validated['template_id']);
            if ($tmpl && $tmpl->logo_path) {
                $overrides['logo_path'] = storage_path('app/public/'.$tmpl->logo_path);
            }
        }

        $validated['overrides'] = $overrides;

        try {
            $filename = $this->pptEngineService->bulkGenerateFromBlocks(
                (string) $validated['template_id'],
                $validated['blocks'],
                $validated['overrides']
            );

            return response()->json([
                'status' => 'queued_bulk',
                'download_url' => url('/ppt/download/'.$filename),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Download file ──────────────────────────────────────────────────────
    public function download($filename)
    {
        $path = storage_path('app/public/'.$filename);
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    // ── Extract text from .pptx ────────────────────────────────────────────
    public function extractText(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:ppt,pptx,potx|max:20480']);
        $file = $request->file('file');
        $path = $file->storeAs('temp_extract', time().'_'.$file->getClientOriginalName(), 'local');
        $fullPath = Storage::disk('local')->path($path);
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

        try {
            $text = $this->pptEngineService->extractTextFromPpt($fullPath);
            Storage::disk('local')->delete($path);

            return response()->json(['status' => 'success', 'text' => $text]);
        } catch (\Exception $e) {
            Storage::disk('local')->delete($path);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── SERMON: Parse uploaded file (PDF, PPTX, TXT) ──────────────────────────
    public function sermonParsePdf(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:pdf,pptx,txt|max:51200']);
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $stored = $file->storeAs('temp_sermon', time().'_'.$file->getClientOriginalName(), 'local');
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, Storage::disk('local')->path($stored));

        $result = $this->runParserScript(['file' => $fullPath]);
        Storage::disk('local')->delete($stored);

        return response()->json($result);
    }

    // ── SERMON: Parse raw text ─────────────────────────────────────────────────
    public function sermonParseText(Request $request)
    {
        $text = $request->input('text', '');
        if (empty(trim($text))) {
            return response()->json(['status' => 'error', 'message' => 'No text provided'], 422);
        }
        $tmpFile = sys_get_temp_dir().'/sermon_text_'.time().'.json';
        file_put_contents($tmpFile, json_encode(['text' => $text], JSON_UNESCAPED_UNICODE));
        $result = $this->runParserScript(['file' => $tmpFile]);
        @unlink($tmpFile);

        return response()->json($result);
    }

    /**
     * Run sermon_parser.py with a file payload and return the decoded result array.
     */
    private function runParserScript(array $source): array
    {
        return $this->pptEngineService->parseSermonFile($source);
    }

    // ── Sermon Dual Export (Full + Live PPTX) ─────────────────────────────────
    public function sermonAnalyzeTemplate(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:pptx|max:51200']);
        $file = $request->file('file');
        $stored = $file->storeAs('sermon_custom_templates', 'template_'.time().'.pptx', 'public');
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, Storage::disk('public')->path($stored));

        try {
            $result = $this->pptEngineService->analyzeTemplate($fullPath);
            $result['template_file'] = $fullPath;

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function sermonGenerate(Request $request)
    {
        $slides = $request->input('slides', []);
        $mode = $request->input('mode', 'both');
        $bannerRatio = (float) $request->input('banner_ratio', 0.26);

        if (empty($slides)) {
            return response()->json(['status' => 'error', 'message' => 'No slides provided'], 422);
        }

        $ts = time();
        $outLive = storage_path("app/public/sermon_live_{$ts}.pptx");
        $outFull = storage_path("app/public/sermon_full_{$ts}.pptx");

        $payload = [
            'mode' => $mode,
            'banner_ratio' => $bannerRatio,
            'slides' => $slides,
            'output_live' => $outLive,
            'output_full' => $outFull,
            'template_file' => $request->input('template_file'),
            'template_mapping' => $request->input('template_mapping', []),
        ];

        try {
            $result = $this->pptEngineService->generateSermon($payload);

            $urls = [];
            if (! empty($result['live'])) {
                $urls['live'] = url('/ppt/sermon/download/'.basename($result['live']));
            }
            if (! empty($result['full'])) {
                $urls['full'] = url('/ppt/sermon/download/'.basename($result['full']));
            }

            return response()->json(['status' => 'success', 'urls' => $urls]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ── Download sermon PPTX ──────────────────────────────────────────────────
    public function sermonDownload(string $filename)
    {
        $allowed = ['pptx'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (! in_array($ext, $allowed) || ! preg_match('/^sermon_(live|full)_\d+\.pptx$/', $filename)) {
            abort(404);
        }
        $path = storage_path('app/public/'.$filename);
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
}
