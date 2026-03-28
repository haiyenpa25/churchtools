<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\BibleLearning\Services\EntityResolutionService;
use Modules\BibleLearning\Services\GeminiExtractionService;
use Modules\BibleLearning\Services\ImportTrackerService;

class ExtractBibleChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 phút timeout cho API Gemini

    public $tries = 3;     // Thử lại 3 lần nếu API lỗi (Rate limit)

    public $backoff = 10;  // Chờ 10 giây trước khi thử lại nếu Job bị fail (RẤT QUAN TRỌNG ĐỂ CHỐNG SPAM)

    protected string $textChunk;

    protected string $bookName;

    protected int $chapter;

    protected string $versesRange;

    protected string $category;

    protected string $fileName;

    protected string $fileHash;

    public function __construct(string $textChunk, string $bookName, int $chapter, string $versesRange, string $category, string $fileName, string $fileHash)
    {
        $this->textChunk = $textChunk;
        $this->bookName = $bookName;
        $this->chapter = $chapter;
        $this->versesRange = $versesRange;
        $this->category = $category;
        $this->fileName = $fileName;
        $this->fileHash = $fileHash;
    }

    public function handle(GeminiExtractionService $aiService, EntityResolutionService $resolutionService, ImportTrackerService $trackerService): void
    {
        Log::info("Processing Bible Chunk: {$this->bookName} {$this->chapter}:{$this->versesRange}");

        // BẢO VỆ API QUOTA TRƯỚC KHI GỌI: Ngủ 4 giây ngay từ đầu để hãm tốc độ của mọi Worker
        sleep(4);

        $context = "Bối cảnh: Cuốn sách {$this->bookName}, đoạn {$this->chapter}. Hãy trích xuất các Thực thể (Nhân vật, Địa danh, Sự kiện, Khái niệm) và Quan hệ (Edges) một cách chính xác dựa trên đoạn văn bản này.";

        try {
            $results = $aiService->extract($this->textChunk, $context);
        } catch (\Exception $e) {
            // Nếu lỗi 429 Too Many Requests, tự động đẩy lại mạng sau 15 giây
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                Log::warning("Rate Limit 429 Hit cho {$this->bookName}. Lùi lại 15s...");
                $this->release(15);

                return;
            }
            throw $e; // Quăng ra để Laravel tính là 1 lần Failed
        }

        if (empty($results)) {
            Log::warning("No entities extracted for {$this->bookName} {$this->chapter}:{$this->versesRange}");

            return;
        }

        $sourceRef = "{$this->bookName} {$this->chapter}:{$this->versesRange}";
        $metadata = ['source_verse' => $sourceRef];

        $nodeMap = []; // Cache local label -> BlNode id để map edge

        $nodesAdded = 0;
        $edgesAdded = 0;

        // 1. Quét tạo Nodes trước
        foreach ($results as $item) {
            if (($item['type'] ?? '') === 'node') {
                $data = $item['raw_data'] ?? $item;
                $label = $data['label'] ?? '';
                $group = $data['group'] ?? 'person';
                $desc = $data['description'] ?? '';

                if ($label) {
                    $node = $resolutionService->resolveNode($label, $group, $desc, $metadata);
                    if ($node) {
                        $nodesAdded++;
                        // Lưu name dạng lowercase để map edge dễ dàng
                        $nodeMap[mb_strtolower($label, 'UTF-8')] = $node;
                    }
                }
            }
        }

        // 2. Quét tạo Edges sau khi đã có Nodes
        foreach ($results as $item) {
            if (($item['type'] ?? '') === 'edge') {
                $data = $item['raw_data'] ?? $item;
                $sourceLabel = mb_strtolower($data['source_node_id'] ?? '', 'UTF-8');
                $targetLabel = mb_strtolower($data['target_node_id'] ?? '', 'UTF-8');
                $relationship = $data['relationship'] ?? 'related_to';

                if (isset($nodeMap[$sourceLabel]) && isset($nodeMap[$targetLabel])) {
                    $resolutionService->createRelationship(
                        $nodeMap[$sourceLabel],
                        $nodeMap[$targetLabel],
                        $relationship,
                        $metadata
                    );
                    $edgesAdded++;
                } else {
                    // Nếu edge map tới node không có trong mảng này, ta cũng có thể resolve nhanh
                    // Nhưng để an toàn và tránh rác, ta chỉ map những node AI vừa nhận diện được trong cùng chunk.
                    Log::debug("Edge skipped due to missing node context: $sourceLabel -> $targetLabel");
                }
            }
        }

        // BÁO CÁO CHO TRACKER: Chunk này đã hoàn thành, cộng thêm count nodes/edges
        $trackerService->incrementChunk($this->category, $this->fileName, $nodesAdded, $edgesAdded);

        Log::info("Finished Processing Chunk: {$sourceRef} (Nodes: $nodesAdded, Edges: $edgesAdded)");
    }

    /**
     * Nắm bắt sự kiện AI sập liên hoàn 3 lần (Rate Limit lố hoặc Lỗi ngầm)
     */
    public function failed(\Throwable $exception)
    {
        $tracker = app(ImportTrackerService::class);
        $errorMsg = substr($exception->getMessage(), 0, 1000);
        $tracker->markAsFailed($this->category, $this->fileName, "Lỗi Chunk {$this->chapter}:{$this->versesRange} - ".$errorMsg);
        Log::error("ExtractBibleChunkJob FAILED for {$this->fileName} chunk. Tracker marked as failed.");
    }
}
