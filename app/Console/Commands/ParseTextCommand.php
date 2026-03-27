<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Modules\BibleLearning\Services\EntityResolutionService;
use Modules\BibleLearning\Services\GeminiExtractionService;

class ParseTextCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bible:parse-text 
                            {--text= : Phân tích văn bản gõ trực tiếp}
                            {--file= : Đường dẫn file .txt để phân tích}
                            {--context= : (Tùy chọn) Sách hoặc Ngữ cảnh (vd: Phim-lê-môn) để AI nhận diện đồng âm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chạy AI phân giải Knowledge Graph lập tức cho 1 đoạn VĂN BẢN (Text) hoặc 1 FILE tự do do bạn cung cấp.';

    /**
     * Execute the console command.
     */
    public function handle(GeminiExtractionService $geminiService, EntityResolutionService $resolutionService)
    {
        $text = $this->option('text');
        $file = $this->option('file');
        $context = $this->option('context') ?? 'Kinh Thánh Tổng Hợp';

        if (! $text && ! $file) {
            $this->error('Bạn PHẢI cung cấp tùy chọn --text="Văn bản..." HOẶC --file="/path/to/file.txt".');

            return 1;
        }

        if ($file) {
            if (! File::exists($file)) {
                $this->error("Không tìm thấy file: {$file}");

                return 1;
            }
            $text = File::get($file);
            $this->info('Đã tải '.strlen($text)." byte ký tự từ file {$file}.");
        }

        $this->info("Bắt đầu xử lý AI cho đoạn văn bản (Content Context: {$context})...");

        // Gọi AI Service (Làm đồng bộ trực tiếp mà không qua Queue để người dùng thấy kết quả ngay)
        $this->line('⏳ Chờ Google AI phân giải thực thể...');
        $aiResult = $geminiService->extractEntitiesAndRelationships($text, $context);

        if (empty($aiResult)) {
            $this->warn('⚠️ API không trả về kết quả JSON hợp lệ nào! Thử lại bằng thuật toán chia nhỏ hoặc kiểm tra mạng mạng, Gemini Key.');

            return 1;
        }

        $this->info('✅ Nhận được phản hồi AI. Đang khởi tạo Nút và Mối Quan Hệ (Resolving Entities)...');

        $metadata = [
            'source_verse' => $context,
            'is_manual' => true,
        ];

        // 1. Phân giải Nút
        $nodeMap = [];
        if (isset($aiResult['nodes']) && is_array($aiResult['nodes'])) {
            $this->line(' - Nạp '.count($aiResult['nodes']).' Thực thể (Nodes)...');
            foreach ($aiResult['nodes'] as $n) {
                if (empty($n['id']) || empty($n['label'])) {
                    continue;
                }

                $resolvedNode = $resolutionService->resolveNode(
                    $n['label'],
                    $n['group'] ?? 'unknown',
                    $n['description'] ?? null,
                    $metadata
                );

                if ($resolvedNode) {
                    $nodeMap[$n['id']] = $resolvedNode;
                    $this->line('   + Thêm Node: '.$resolvedNode->label);
                }
            }
        }

        // 2. Phân giải Cạnh
        if (isset($aiResult['edges']) && is_array($aiResult['edges'])) {
            $this->line(' - Nạp '.count($aiResult['edges']).' Cạnh (Edges)...');
            foreach ($aiResult['edges'] as $e) {
                if (empty($e['source']) || empty($e['target']) || empty($e['relationship']) || empty($e['label'])) {
                    continue;
                }

                $sourceId = $e['source'] ?? null;
                $targetId = $e['target'] ?? null;

                if (! isset($nodeMap[$sourceId]) || ! isset($nodeMap[$targetId])) {
                    continue; // Bỏ qua nếu không Map được hai đầu
                }

                $relationText = $e['label'] ?? $e['relationship']; // Gemini thi thoảng dùng label thay vì relationship

                $resolutionService->createRelationship(
                    $nodeMap[$sourceId],
                    $nodeMap[$targetId],
                    $relationText,
                    $metadata
                );

                $this->line("   + Thêm Quan hệ: [{$nodeMap[$sourceId]->label}] -> [{$nodeMap[$targetId]->label}]: {$relationText}");
            }
        }

        $this->info('🚀 XONG! Database đã được cập nhật thành công.');

        return 0;
    }
}
