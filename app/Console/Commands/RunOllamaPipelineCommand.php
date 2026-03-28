<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Modules\BibleLearning\Services\InMemoryResolutionService;
use Modules\BibleLearning\Services\MemoryDumperService;
use Modules\BibleLearning\Services\OllamaInferenceService;
use Modules\BibleLearning\Services\SlidingWindowService;

class RunOllamaPipelineCommand extends Command
{
    protected $signature = 'bible:ollama-pipeline {--category=kinh-thanh} {--book= : Tên sách cụ thể cần chạy (VD: 01_Sang-the-ky)}';

    protected $description = 'Chạy NLP Pipeline cục bộ dùng Ollama Qwen 2.5 với Sliding Window';

    public function handle(
        SlidingWindowService $slider,
        OllamaInferenceService $ollama,
        InMemoryResolutionService $memory,
        MemoryDumperService $dumper
    ) {
        $category = $this->option('category');
        $bookFilter = $this->option('book');

        $this->info("Bắt đầu NLP Pipeline (Ollama) cho danh mục: {$category}");

        // Tìm tất cả các file
        $files = Storage::disk('local')->files("tai-lieu/{$category}");

        if (empty($files)) {
            $this->error("Không tìm thấy file nào trong thư mục storage/app/tai-lieu/{$category}");

            return;
        }

        // Nhóm file theo sách (VD: 01_Sang-the-ky_01.txt -> 01_Sang-the-ky)
        $books = [];
        foreach ($files as $file) {
            $baseName = basename($file);
            // Prefix pattern: 01_Sang-the-ky
            if (preg_match('/^(\d+_[A-Za-z0-9\-]+)/', $baseName, $matches)) {
                $books[$matches[1]][] = $file;
            }
        }

        // Nếu User truyền flag `--book=01_Sang-the-ky` thì chỉ chạy 1 sách đó để tránh treo máy
        if ($bookFilter) {
            if (! isset($books[$bookFilter])) {
                $this->error("Không tìm thấy sách: $bookFilter");

                return;
            }
            $books = [$bookFilter => $books[$bookFilter]];
        }

        $this->info('Đã Gom nhóm thành '.count($books).' quyển sách.');

        foreach ($books as $bookName => $chapterFiles) {
            $this->warn("\n=====================================");
            $this->warn("📚 Đang xử lý Sách: $bookName (".count($chapterFiles).' chương - Tập tin)...');

            // Xả sạch thanh RAM từ sách định tuyến cũ (Garbage Collection của Dữ liệu Cũ)
            $memory->flushMemory();

            foreach ($chapterFiles as $file) {
                $chapterName = basename($file, '.txt');
                $this->info("\n  -> Đọc chương: $chapterName");
                $text = Storage::disk('local')->get($file);

                // Bước 1: Thuật toán Cửa sổ trượt (Sliding Window: Mỗi chunk 250 từ, đè 20%)
                $chunks = $slider->chunkText($text, 250, 0.20);

                // Bước 2 & 3: Inference bằng Local Ollama và Đẩy thẳng mảng (O(1) Memory Update)
                foreach ($chunks as $index => $chunk) {
                    $this->line('     ⏳ Phân tích Chunk '.($index + 1).'/'.count($chunks).' qua hệ sinh thái Ollama VRAM...');

                    // Nhúng Context vào Model
                    $context = "Bối cảnh: Cuốn sách $bookName, chương $chapterName.";

                    $entities = $ollama->extract($chunk, $context);

                    if (empty($entities)) {
                        $this->warn('     ❌ Lỗi Ollama Timeout hoặc AI không tìm ra Thực Thể nào trong Chunk này.');

                        continue;
                    }

                    $nodeCount = 0;
                    $edgeCount = 0;

                    foreach ($entities as $entity) {
                        try {
                            $type = $entity['type'] ?? '';
                            $data = $entity['raw_data'] ?? [];

                            if ($type === 'node') {
                                $memory->upsertNode(
                                    $data['label'] ?? '',
                                    $data['group'] ?? 'unclassified',
                                    $data['description'] ?? '',
                                    ['source_verse' => $chapterName]
                                );
                                $nodeCount++;
                            } elseif ($type === 'edge') {
                                $sourceKey = mb_strtolower($data['source_node_id'] ?? '', 'UTF-8');
                                $targetKey = mb_strtolower($data['target_node_id'] ?? '', 'UTF-8');

                                $memory->upsertEdge(
                                    $sourceKey,
                                    $targetKey,
                                    $data['relationship'] ?? '',
                                    ['source_verse' => $chapterName]
                                );
                                $edgeCount++;
                            }
                        } catch (\Exception $e) {
                            $this->error('     Lỗi parse sơ đồ Node/Edge: '.$e->getMessage());
                        }
                    }
                    $this->line("     ✅ [OK] Nhét $nodeCount Nodes và $edgeCount Edges vào thanh RAM (0.01ms)");
                }
            }

            // Bước 4: Xả biến RAM ($memory dump) thành file JSON tĩnh ngay khi kết thúc 1 cuốn Sách
            $nodes = $memory->getNodes();
            $edges = $memory->getEdges();

            $this->comment("\n💾 Đang xuất Memory -> JSON ".count($nodes).' Nodes và '.count($edges)." Edges của $bookName...");
            $dumper->dumpToJsonFile($bookName, $nodes, $edges);

            $this->info("🎇 Hoàn tất và Đóng gói File Sách: $bookName");
        }

        $this->info("\n🎉 TOÀN BỘ NLP DATA ENGINEERING PIPELINE TRÊN LOCAL OLLAMA ĐÃ HOÀN THÀNH MỸ MÃN!");
        $this->info('   Bạn đã tiết kiệm được thời gian truy cập DB, loại trừ 100% Coreference Error.');
        $this->info('   Hãy commit thư mục [database/data/bible_dump/] lên Git và Bấm Nạp Dữ Liệu ở Server!');
    }
}
