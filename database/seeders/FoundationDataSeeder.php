<?php

namespace Database\Seeders;

use App\Models\BibleBook;
use App\Models\BibleChapter;
use App\Models\BibleVerse;
use App\Models\Song;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class FoundationDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Đang dọn dẹp Database chuẩn bị Gieo Mầm Foundation Data...');

        // 1. Dọn sạch DB
        Schema::disableForeignKeyConstraints();
        BibleVerse::truncate();
        BibleChapter::truncate();
        BibleBook::truncate();
        Song::truncate();
        Schema::enableForeignKeyConstraints();

        // Tắt Query Log để giải phóng RAM
        DB::disableQueryLog();

        // 2. Nạp dữ liệu Kinh Thánh
        $bibleFile = database_path('data/foundation/bible.json');
        if (File::exists($bibleFile)) {
            $this->command->info('📖 Đang tiến hành đọc và nạp cấu trúc Kinh Thánh...');
            $bibleData = json_decode(File::get($bibleFile), true);
            $now = now();

            foreach ($bibleData as $bookData) {
                // Sáng-thế Ký -> Number 1
                $bookNumber = (int) preg_replace('/[^0-9].*$/', '', $bookData['book_key']);

                $book = BibleBook::create([
                    'name' => $bookData['book'],
                    'book_number' => $bookNumber,
                    'chapter_count' => count($bookData['chapters']),
                ]);

                foreach ($bookData['chapters'] as $chapterData) {
                    $chapter = BibleChapter::create([
                        'bible_book_id' => $book->id,
                        'chapter_number' => $chapterData['chapter'],
                        'verse_count' => $chapterData['verseCount'],
                    ]);

                    $versesBatch = [];
                    foreach ($chapterData['verses'] as $verseData) {
                        $versesBatch[] = [
                            'bible_chapter_id' => $chapter->id,
                            'verse_number' => $verseData['verseNumber'],
                            'content' => $verseData['content'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    // Bơm nguyên 1 đoạn Kinh Thánh (Khoảng 30-50 câu) cùng lúc
                    BibleVerse::insert($versesBatch);
                }
            }
            $this->command->info('✅ Nạp Kinh Thánh Siêu Tốc hoàn tất!');
        } else {
            $this->command->warn('⚠️ Không tìm thấy bible.json tại Foundation!');
        }

        // 3. Nạp dữ liệu Bài Hát
        $songsFile = database_path('data/foundation/songs.json');
        if (File::exists($songsFile)) {
            $this->command->info('🎵 Đang tiến hành đọc và nạp Thánh Ca (Worship Songs)...');
            $songsData = json_decode(File::get($songsFile), true);

            $now = now();
            // Chia lô 100 row để nạp vào DB tránh qúa tải
            collect($songsData)->map(function ($song) use ($now) {
                if (isset($song['created_at'])) $song['created_at'] = \Carbon\Carbon::parse($song['created_at'])->format('Y-m-d H:i:s');
                else $song['created_at'] = $now;
                
                if (isset($song['updated_at'])) $song['updated_at'] = \Carbon\Carbon::parse($song['updated_at'])->format('Y-m-d H:i:s');
                else $song['updated_at'] = $now;
                
                return $song;
            })->chunk(100)->each(function ($chunk) {
                Song::insert($chunk->toArray());
            });
            $this->command->info('✅ Nạp Bài hát hoàn tất!');
        } else {
            $this->command->warn('⚠️ Không tìm thấy songs.json tại Foundation!');
        }
    }
}
