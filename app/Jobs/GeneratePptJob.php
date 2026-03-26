<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function handle(): void
    {
        $enginePath = base_path('engine/ppt_generator.py');
        $pythonPath = base_path('engine/venv/Scripts/python.exe');

        // Fallback to global python if venv not found
        if (! file_exists($pythonPath)) {
            $pythonPath = 'python';
        }

        if (is_string($this->payload) && file_exists($this->payload)) {
            // It's a file containing the JSON payload exactly to avoid CMD encoding issues
            $command = "\"{$pythonPath}\" \"{$enginePath}\" --file \"{$this->payload}\"";
        } else {
            // Backward compatibility for array payloads passed directly via CMD
            $jsonPayload = empty($this->payload) ? '{}' : json_encode($this->payload, JSON_UNESCAPED_UNICODE);
            $escapedPayload = addslashes($jsonPayload);
            $command = "\"{$pythonPath}\" \"{$enginePath}\" \"{$escapedPayload}\"";
        }

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            Log::error('PPT Generation Failed', ['output' => $output, 'command' => $command]);
        } else {
            Log::info('PPT Generated Successfully', ['output' => $output]);
        }
    }
}
