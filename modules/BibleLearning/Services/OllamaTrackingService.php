<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Cache;

class OllamaTrackingService
{
    const CACHE_KEY = 'ollama_pipeline_status';

    public function getStatus(): array
    {
        return Cache::get(self::CACHE_KEY, [
            'is_running' => false,
            'progress_percent' => 0,
            'current_book' => '',
            'current_chapter' => '',
            'logs' => [],
            'error' => null,
        ]);
    }

    public function start(): void
    {
        Cache::put(self::CACHE_KEY, [
            'is_running' => true,
            'progress_percent' => 0,
            'current_book' => 'Đang khởi động',
            'current_chapter' => '',
            'logs' => ['[System] Bắt đầu kích hoạt Local NLP Pipeline...'],
            'error' => null,
        ], now()->addHours(6)); // Timeout 6 tiếng cho an toàn
    }

    public function updateProgress(int $percent, string $book, string $chapter): void
    {
        $status = $this->getStatus();
        $status['progress_percent'] = $percent;
        $status['current_book'] = $book;
        $status['current_chapter'] = $chapter;
        Cache::put(self::CACHE_KEY, $status, now()->addHours(6));
    }

    public function addLog(string $message): void
    {
        $status = $this->getStatus();
        // Cắt bớt giữ lại 100 dòng log cuối để tránh tràn Array RAM
        $status['logs'][] = '['.date('H:i:s').'] '.$message;
        if (count($status['logs']) > 100) {
            array_shift($status['logs']);
        }
        Cache::put(self::CACHE_KEY, $status, now()->addHours(6));
    }

    public function stop(?string $error = null): void
    {
        $status = $this->getStatus();
        $status['is_running'] = false;
        if ($error) {
            $status['error'] = $error;
            $this->addLog('[LỖI MẠNG] '.$error);
        } else {
            $status['progress_percent'] = 100;
            $this->addLog('[Thành Công] Toàn bộ Pipeline đã hoàn tất!');
        }
        Cache::put(self::CACHE_KEY, $status, now()->addHours(6));
    }
}
