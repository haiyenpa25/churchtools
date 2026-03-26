<?php

namespace Modules\PptLivestream\Services;

use App\Jobs\GeneratePptJob;
use App\Models\PptTemplate;
use Modules\PptLivestream\Contracts\PptEngineServiceInterface;
use Modules\PptLivestream\Contracts\TemplateRepositoryInterface;

class PptEngineService implements PptEngineServiceInterface
{
    public function __construct(
        protected TemplateRepositoryInterface $templateRepository
    ) {}

    public function generateFromPayload(array $payload): void
    {
        // Business logic here, currently just dispatching the job
        GeneratePptJob::dispatch($payload);
    }

    public function bulkGenerateFromBlocks(string $templateId, array $blocks, array $overrides = []): string
    {
        $template = PptTemplate::with('presets')->findOrFail($templateId);

        // Ensure we retrieve a pure array to avoid nested "0" keys when JSON encoded
        $presetModel = $template->presets()->first();
        $preset = $presetModel ? $presetModel->toArray() : [];

        // Merge positional overrides
        if (! empty($overrides['x'])) {
            $preset['x'] = (float) $overrides['x'];
        }
        if (! empty($overrides['y'])) {
            $preset['y'] = (float) $overrides['y'];
        }
        if (! empty($overrides['width'])) {
            $preset['width'] = (float) $overrides['width'];
        }
        if (! empty($overrides['height'])) {
            $preset['height'] = (float) $overrides['height'];
        }

        // Build font config, merging DB defaults with user overrides
        $fontConfig = [];
        if (! empty($preset['font_config'])) {
            $fontConfig = is_string($preset['font_config']) ? json_decode($preset['font_config'], true) : $preset['font_config'];
        }
        if (! empty($overrides['font_size'])) {
            $fontConfig['size'] = (int) $overrides['font_size'];
        }
        if (! empty($overrides['font_color'])) {
            $fontConfig['color'] = $overrides['font_color'];
        }

        // ── Merge color theme overrides into font_config so Python reads them from fc_override ──
        $colorKeys = ['banner_color', 'logo_border_color', 'logo_bg_color', 'accent_color', 'text_color'];
        foreach ($colorKeys as $key) {
            if (! empty($overrides[$key])) {
                $fontConfig[$key] = $overrides[$key];
                // text_color maps to 'color' in fc_override
                if ($key === 'text_color') {
                    $fontConfig['color'] = $overrides[$key];
                }
            }
        }

        $preset['font_config'] = $fontConfig;

        // Pass logo path into preset so Python sets logo_override correctly
        if (! empty($overrides['logo_path'])) {
            $preset['logo_path'] = $overrides['logo_path'];
        }

        // Always enable green screen for livestream use
        $preset['is_green_screen'] = true;

        $filename = 'bulk_generated_'.time().'.pptx';
        $fullOutput = storage_path('app/public/'.$filename);

        $templatePath = $template->file_path;
        if (! file_exists($templatePath) && ! str_starts_with($templatePath, 'C:\\') && ! str_starts_with($templatePath, '/') && ! str_contains($templatePath, ':\\')) {
            $templatePath = storage_path('app/public/'.$templatePath);
        }

        $payload = [
            'action' => 'bulk_blocks',
            'output_file' => $fullOutput,
            'template' => $templatePath,
            'preset' => $preset,
            'blocks' => $blocks,
        ];

        // Save Payload to a temporary JSON file to avoid Windows CLI Unicode corruption
        $tempPayloadPath = storage_path('app/public/temp_payload_'.time().'_'.uniqid().'.json');
        file_put_contents($tempPayloadPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // Dispatch background job with the absolute file path instead of string payload
        GeneratePptJob::dispatch($tempPayloadPath);

        return $filename;
    }

    public function extractTextFromPpt(string $filePath): string
    {
        $enginePath = base_path('engine/ppt_extractor.py');
        // Use venv python to bypass Windows App execution alias
        $pythonCommand = base_path('engine/venv/Scripts/python.exe');
        if (! file_exists($pythonCommand)) {
            $pythonCommand = 'python'; // Fallback
        }

        $escapedFilePath = escapeshellarg($filePath);
        $command = "\"$pythonCommand\" \"$enginePath\" $escapedFilePath 2>&1";

        $output = shell_exec($command);
        $result = json_decode($output, true);

        if (! $result || ! isset($result['status'])) {
            throw new \Exception('Python Failed to extract text. Output: '.$output);
        }

        if ($result['status'] !== 'success') {
            throw new \Exception('Python Exception: '.($result['message'] ?? 'Unknown error'));
        }

        return $result['text'];
    }

    /**
     * Parse pdf/txt into blocks using python.
     */
    public function parseSermonFile(array $source): array
    {
        $engineDir = base_path('engine');
        $scriptPath = $engineDir.DIRECTORY_SEPARATOR.'sermon_parser.py';
        $pythonExe = file_exists($engineDir.'/venv/Scripts/python.exe')
            ? $engineDir.'/venv/Scripts/python.exe'
            : env('PYTHON_PATH', 'python');

        $tmpPayload = sys_get_temp_dir().'/sermon_src_'.time().'.json';
        file_put_contents($tmpPayload, json_encode($source, JSON_UNESCAPED_UNICODE));

        $apiKey = env('GEMINI_API_KEY');
        $aiFlag = $apiKey ? ' --ai --api-key '.escapeshellarg($apiKey) : '';

        $cmd = escapeshellarg($pythonExe).' '.escapeshellarg($scriptPath)
             .' --file '.escapeshellarg($tmpPayload).$aiFlag.' 2>&1';

        $output = [];
        $exitCode = 0;
        exec('cd '.escapeshellarg($engineDir).' && '.$cmd, $output, $exitCode);
        @unlink($tmpPayload);

        $jsonLine = '';
        foreach ($output as $line) {
            if (str_starts_with(trim($line), '{')) {
                $jsonLine = $line;
                break;
            }
        }
        if (! $jsonLine) {
            return ['status' => 'error', 'message' => 'Parser returned no output', 'raw' => implode("\n", $output)];
        }
        $result = json_decode($jsonLine, true);

        return $result ?: ['status' => 'error', 'message' => 'Invalid JSON from parser'];
    }

    /**
     * Analyze template via python script.
     */
    public function analyzeTemplate(string $fullPath): array
    {
        $engineDir = base_path('engine');
        $scriptPath = $engineDir.DIRECTORY_SEPARATOR.'template_analyzer.py';
        $pythonExe = file_exists($engineDir.'/venv/Scripts/python.exe')
            ? $engineDir.'/venv/Scripts/python.exe'
            : env('PYTHON_PATH', 'python');

        $cmd = escapeshellarg($pythonExe).' '.escapeshellarg($scriptPath)
             .' '.escapeshellarg($fullPath).' 2>&1';

        $output = [];
        $exitCode = 0;
        exec('cd '.escapeshellarg($engineDir).' && '.$cmd, $output, $exitCode);

        $jsonLine = '';
        foreach ($output as $line) {
            if (str_starts_with(trim($line), '{')) {
                $jsonLine = $line;
                break;
            }
        }

        $result = $jsonLine ? json_decode($jsonLine, true) : null;
        if (! $result || $result['status'] !== 'success') {
            throw new \Exception('Template analyzer failed: '.implode("\n", $output));
        }

        return $result;
    }

    /**
     * Generate Sermon PPT.
     */
    public function generateSermon(array $payload): array
    {
        $ts = time();
        $tmpFile = sys_get_temp_dir().'/sermon_payload_'.$ts.'.json';
        file_put_contents($tmpFile, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $engineDir = base_path('engine');
        $script = $engineDir.DIRECTORY_SEPARATOR.'sermon_generator.py';
        $python = file_exists($engineDir.'/venv/Scripts/python.exe')
            ? $engineDir.'/venv/Scripts/python.exe'
            : env('PYTHON_PATH', 'python');

        $cmd = escapeshellarg($python).' '.escapeshellarg($script).' --file '.escapeshellarg($tmpFile).' 2>&1';
        $cwd = $engineDir;
        $output = [];
        $exitCode = 0;

        exec('cd '.escapeshellarg($cwd).' && '.$cmd, $output, $exitCode);
        @unlink($tmpFile);

        $jsonLine = '';
        foreach ($output as $line) {
            if (str_starts_with(trim($line), '{')) {
                $jsonLine = $line;
                break;
            }
        }
        $result = $jsonLine ? json_decode($jsonLine, true) : null;

        if (! $result || $result['status'] !== 'success') {
            throw new \Exception($result['message'] ?? 'Python engine error: '.implode("\n", $output));
        }

        return $result;
    }
}
