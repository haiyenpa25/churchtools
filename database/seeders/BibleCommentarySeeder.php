<?php

namespace Database\Seeders;

use App\Models\BibleBook;
use App\Models\BibleCommentary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BibleCommentarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📖 Bắt đầu nạp dữ liệu Giải Nghĩa Kinh Thánh...');

        DB::disableQueryLog();
        BibleCommentary::truncate();

        $parsedDir = public_path('tai-lieu/kinh-thanh-giai-nghia/parsed');

        if (!File::exists($parsedDir)) {
            $parsedDir = base_path('tai-lieu/kinh-thanh-giai-nghia/parsed');
            if (!File::exists($parsedDir)) {
                $this->command->warn('⚠️ Không tìm thấy thư mục: ' . $parsedDir);
                return;
            }
        }

        $files = File::files($parsedDir);
        $count = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'json') continue;

            $filename = $file->getFilename();
            // Lấy 2 số đầu của prefix. VD: "01_sang-the-ky.json" => 1
            if (preg_match('/^(\d+)_/', $filename, $matches)) {
                $fileNum = (int) $matches[1];
            } else {
                continue;
            }

            // Ánh xạ sang Book ID chuẩn (lý do: Thiếu Nhã Ca và Ca Thương)
            $bookId = $fileNum;
            if ($fileNum >= 22 && $fileNum <= 23) {
                // VD: 22(Ê-sai) -> book_number 23. 23(Giê-rê-mi) -> book_number 24
                $bookId = $fileNum + 1;
            } else if ($fileNum >= 24) {
                // VD: 24(Ê-xê-chi-ên) -> book_number 26 vì trước đó rớt mất Ca Thương (25)
                $bookId = $fileNum + 2;
            }

            $book = BibleBook::where('book_number', $bookId)->first();
            if (!$book) {
                $this->command->warn("Skipping file {$filename} because book_number {$bookId} not found.");
                continue;
            }

            $data = json_decode(File::get($file->getPathname()), true);
            if (!$data || empty($data['chapters'])) {
                continue;
            }

            $insertData = [];
            $now = now();

            foreach ($data['chapters'] as $chapter) {
                $refString = $chapter['reference'] ?? null;
                $title = $chapter['title'] ?? null;
                $verses = $chapter['verses'] ?? [];
                
                // Nối nội dung các verse lại để lưu trữ (Full Content)
                $fullContent = "";
                foreach ($verses as $v) {
                    if (!empty($v['reference'])) {
                        $fullContent .= "\n\n(" . $v['reference'] . ") ";
                    } else {
                        $fullContent .= "\n\n";
                    }
                    $fullContent .= $v['content'];
                }

                $insertData[] = [
                    'bible_book_id' => $book->id,
                    'reference_string' => $refString,
                    'title' => $title,
                    'content' => trim($fullContent),
                    'raw_data' => json_encode($verses, JSON_UNESCAPED_UNICODE),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Gieo vào Database (Chunk 10 rows / time)
            foreach (array_chunk($insertData, 10) as $chunk) {
                BibleCommentary::insert($chunk);
            }
            
            $count++;
            $this->command->info("✅ Đã nạp thành công sách: {$book->name} (" . count($insertData) . " mục)");
        }

        $this->command->info("🎉 Nạp dữ liệu Giải Nghĩa hoàn tất! Đã xử lý {$count} sách.");
    }
}
