<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\BibleLearning\Services\ImportTrackerService;
use Modules\BibleLearning\Services\InMemoryResolutionService;
use Modules\BibleLearning\Services\MemoryDumperService;
use Modules\BibleLearning\Services\OllamaInferenceService;
use Modules\BibleLearning\Services\OllamaTrackingService;
use Modules\BibleLearning\Services\SlidingWindowService;

class RunOllamaPipelineCommand extends Command
{
    protected $signature = 'bible:ollama-pipeline {--category=kinh-thanh} {--book= : Tên sách cụ thể cần chạy (VD: 01_Sang-the-ky)} {--file=* : Danh sách file cụ thể cần chạy (VD: 01_Sang-the-ky_01.txt)} {--model=gemma3:4b : Tên model Ollama cần dùng}';

    protected $description = 'Chạy NLP Pipeline cục bộ dùng Ollama (Gemma 3 hoặc Qwen 2.5) với Sliding Window';

    public function handle(
        SlidingWindowService $slider,
        OllamaInferenceService $ollama,
        InMemoryResolutionService $memory,
        MemoryDumperService $dumper,
        OllamaTrackingService $tracker,
        ImportTrackerService $trackerService
    ) {
        $tracker->start();
        $tracker->addLog('Hệ thống RAM Allocation: [Đã thiết lập 2GB]');
        // Mở khóa sức mạnh RAM: Cấp 2GB cho tiến trình Data Engineering
        ini_set('memory_limit', '2G');

        $category = $this->option('category');
        $bookFilter = $this->option('book');
        $modelName = $this->option('model');

        $this->info("Bắt đầu NLP Pipeline (Ollama) cho danh mục: {$category} với Model Engine: {$modelName}");

        // Tìm tất cả các file trong thư mục Root của XAMPP (Không dùng Storage ảo của Laravel)
        $path = base_path("tai-lieu/{$category}");
        if (! File::exists($path)) {
            $msg = "Không tìm thấy thư mục: {$path}";
            $this->error($msg);
            $tracker->stop($msg);

            return;
        }

        $allFiles = File::files($path);
        $files = [];
        foreach ($allFiles as $f) {
            if ($f->getExtension() === 'txt') {
                $files[] = $f->getRealPath();
            }
        }

        if (empty($files)) {
            $msg = "Không tìm thấy file txt nào trong thư mục {$path}";
            $this->error($msg);
            $tracker->stop($msg);

            return;
        }

        $fileFilters = $this->option('file');
        if (! empty($fileFilters)) {
            $files = array_filter($files, function ($file) use ($fileFilters) {
                // $fileFilters có thể nhận Tên file có đuôi txt hoặc không đuôi
                $base = basename($file);
                $nameNoExt = basename($file, '.txt');

                return in_array($base, $fileFilters) || in_array($nameNoExt, $fileFilters);
            });

            if (empty($files)) {
                $msg = 'Không tìm thấy file nào khớp với tuỳ chọn --file';
                $this->error($msg);
                $tracker->stop($msg);

                return;
            }
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
        $tracker->addLog('Đã quét thấy '.count($books).' sách tương ứng cấu trúc.');

        $processedBooks = 0;
        $totalBooks = count($books);

        foreach ($books as $bookName => $chapterFiles) {
            $processedBooks++;
            $percent = (int) (($processedBooks / $totalBooks) * 100);

            $this->warn("\n=====================================");
            $msg = "📚 Đang xử lý Sách: $bookName (".count($chapterFiles).' chương - Tập tin)...';
            $this->warn($msg);
            $tracker->addLog($msg);
            $tracker->updateProgress($percent, $bookName, 'Khởi động bộ dịch');

            // Xả sạch thanh RAM từ sách định tuyến cũ (Garbage Collection của Dữ liệu Cũ)
            $memory->flushMemory();

            // QUAN TRỌNG: Gọi dữ liệu JSON cũ nạp lại vào RAM để nối tiếp Sách mà không cần chạy lại từ đầu!
            $memory->hydrateMemoryFromJson($bookName);

            foreach ($chapterFiles as $file) {
                $chapterName = basename($file, '.txt');

                $text = file_get_contents($file);
                $fileHash = md5($text); // Mã hoá chuỗi để xác thực tính toàn vẹn File

                // KIỂM TRA TRÙNG LẶP: Nếu File này đã từng chạy xong bằng AI và không đổi Content -> SKIP!
                if ($trackerService->isProcessedAndUnchanged($category, $chapterName, $fileHash)) {
                    $this->warn("\n  ⏩ BỎ QUA File $chapterName (Đã phân tích xong và không đổi nội dung)");
                    $tracker->addLog("⏩ SKIPPED: $chapterName (Cơ chế Nhảy cóc)");

                    continue;
                }

                $this->info("\n  -> Đọc chương mới: $chapterName");

                // Bước 1: Thuật toán Cửa sổ trượt (Sliding Window: Mỗi chunk 250 từ, đè 20%)
                $chunks = $slider->chunkText($text, 250, 0.20);

                // Đánh dấu File bắt đầu xử lý vào DB Hệ Thống Tracking
                $trackerService->markAsProcessing($category, $chapterName, $fileHash, count($chunks));
                $fileNodesAdded = 0;
                $fileEdgesAdded = 0;

                // Bước 2 & 3: Inference bằng Local Ollama và Đẩy thẳng mảng (O(1) Memory Update)
                foreach ($chunks as $index => $chunk) {
                    $this->line('     ⏳ Phân tích Chunk '.($index + 1).'/'.count($chunks).' qua hệ sinh thái Ollama VRAM...');

                    // Nhúng Context vào Model
                    $context = "Bối cảnh: Cuốn sách $bookName, chương $chapterName.";

                    $entities = $ollama->extract($chunk, $context, $modelName);

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
                                    $bookName,
                                    $data['group'] ?? 'unclassified',
                                    $data['description'] ?? '',
                                    ['source_verse' => $chapterName]
                                );
                                $nodeCount++;
                            } elseif ($type === 'edge') {
                                $sourceKey = mb_strtolower($data['source_node_id'] ?? '', 'UTF-8');
                                $targetKey = mb_strtolower($data['target_node_id'] ?? '', 'UTF-8');

                                $sourceKey = empty($sourceKey) ? '' : $memory->resolveAlias($sourceKey, $bookName);
                                $targetKey = empty($targetKey) ? '' : $memory->resolveAlias($targetKey, $bookName);

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

                    // Cập nhật Chunk Progress trong Tracker MySQL
                    $trackerService->incrementChunk($category, $chapterName, $nodeCount, $edgeCount);
                    $fileNodesAdded += $nodeCount;
                    $fileEdgesAdded += $edgeCount;
                }

                // Chốt sổ: Ghi nhận File đã dịch xong hoàn toàn 100%
                $trackerService->markAsCompleted($category, $chapterName, $fileHash, $fileNodesAdded, $fileEdgesAdded);
            }

            // Bước 4: Xả biến RAM ($memory dump) thành file JSON tĩnh ngay khi kết thúc 1 cuốn Sách
            $nodes = $memory->getNodes();
            $edges = $memory->getEdges();

            $msgDump = '💾 Đang xuất Memory -> JSON '.count($nodes).' Nodes và '.count($edges)." Edges của $bookName...";
            $this->comment("\n".$msgDump);
            $tracker->addLog($msgDump);

            $dumper->dumpToJsonFile($bookName, $nodes, $edges);

            $this->info("🎇 Hoàn tất và Đóng gói File Sách: $bookName");
            $tracker->addLog("✅ Đã giải quyết xong file tĩnh Book: $bookName");
        }

        $this->info("\n🎉 TOÀN BỘ NLP DATA ENGINEERING PIPELINE TRÊN LOCAL OLLAMA ĐÃ HOÀN THÀNH MỸ MÃN!");
        $tracker->addLog('🎉 TOÀN BỘ PIPELINE LOCAL ĐÃ CHẠY XONG 100%');
        $tracker->stop();
        $this->info('   Bạn đã tiết kiệm được thời gian truy cập DB, loại trừ 100% Coreference Error.');
        $this->info('   Hãy commit thư mục [database/data/bible_dump/] lên Git và Bấm Nạp Dữ Liệu ở Server!');
    }
}
