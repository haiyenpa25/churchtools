<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Jobs\ExtractBibleChunkJob;

class ParseBibleCommand extends Command
{
    protected $signature = 'bible:ingest 
                            {--book= : Nhập từ khóa sách để chạy riêng (VD: "Sang_The_Ky")}
                            {--category=kinh-thanh : Thư mục chứa tài liệu (mặc định: kinh-thanh, kinh-thanh-giai-nghia...)}';

    protected $description = 'Quét dữ liệu Kinh Thánh local từ thư mục tai-lieu, cắt nhỏ và đẩy vào Queue để AI xử lý';

    public function handle()
    {
        $category = $this->option('category');
        $path = base_path("tai-lieu/{$category}");

        if (!File::exists($path)) {
            $this->error("Lỗi: Không tìm thấy thư mục: {$path}");
            return;
        }

        $files = File::files($path);
        
        if (empty($files)) {
            $this->error("Thư mục trống hoặc không có file.");
            return;
        }

        $bookFilter = $this->option('book');
        $jobCount = 0;

        $this->info("Bắt đầu quy trình Chunking & Đẩy vào Queue...");
        $bar = $this->output->createProgressBar(count($files));
        $tracker = app(\Modules\BibleLearning\Services\ImportTrackerService::class);

        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $hash = md5_file($file->getRealPath());
            
            // Lọc theo cờ --book (rất hữu ích để test riêng lẻ)
            if ($bookFilter && !str_contains(mb_strtolower($filename, 'UTF-8'), mb_strtolower($bookFilter, 'UTF-8'))) {
                $bar->advance();
                continue;
            }

            // KIỂM TRA: Nếu file đã chạy xong AI và không bị sửa nội dung -> Bỏ Qua!
            if ($tracker->isProcessedAndUnchanged($category, $filename, $hash)) {
                $this->info("\n[SKIP] Bỏ qua file đã phân tích xong: {$filename}");
                $bar->advance();
                continue;
            }

            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (empty($lines)) {
                $bar->advance();
                continue;
            }

            // Dòng đầu tiên luôn là "Tên sách + Chương", VD: "Ma-thi-ơ 1"
            $bookAndChapterName = trim(array_shift($lines));
            
            preg_match('/^(.*?)\s+?(\d+)$/', $bookAndChapterName, $matches);
            $bookName = trim($matches[1] ?? $bookAndChapterName);
            $chapter = (int) ($matches[2] ?? 0);

            // Cắt 15 câu mỗi đoạn (để AI có đủ context, nhưng không tràn Token)
            $chunkSize = 15;
            $chunks = array_chunk($lines, $chunkSize);
            $totalChunks = count($chunks);

            // Ghi trạng thái Đang Xử Lý với Tổng số Chunk
            $tracker->markAsProcessing($category, $filename, $hash, $totalChunks);

            foreach ($chunks as $chunk) {
                $textChunk = implode("\n", $chunk);
                
                // Lấy số câu
                preg_match('/^(\d+)/', $chunk[0] ?? '', $startMatch);
                preg_match('/^(\d+)/', end($chunk) ?? '', $endMatch);
                
                $startV = $startMatch[1] ?? '?';
                $endV = $endMatch[1] ?? '?';
                $versesRange = ($startV === $endV) ? $startV : "$startV-$endV";

                ExtractBibleChunkJob::dispatch($textChunk, $bookName, $chapter, $versesRange, $category, $filename, $hash);
                $jobCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        // Hoàn tất
        $this->info("✅ Đã bắn THÀNH CÔNG {$jobCount} Jobs vào Queue (Hàng đợi)!");
        $this->line("👉 Để xử lý, bạn mở terminal mới và gõ: <fg=green>php artisan queue:work</>");
    }
}
