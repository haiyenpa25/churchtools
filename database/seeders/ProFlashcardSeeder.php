<?php

namespace Database\Seeders;

use App\Models\BlFlashcard;
use App\Models\BlFlashcardReview;
use App\Models\BlTempEntity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProFlashcardSeeder extends Seeder
{
    public function run()
    {
        // Xóa sạch để làm lại từ đầu (Vô hiệu hóa khóa ngoại tạm thời)
        Schema::disableForeignKeyConstraints();
        BlFlashcardReview::truncate();
        BlFlashcard::truncate();
        BlTempEntity::truncate();
        Schema::enableForeignKeyConstraints();

        // Bơm thẳng 5 thẻ ĐÃ DUYỆT vào Kho Flashcard chính thức
        $cards = [
            [
                'question' => 'Đức Chúa Trời Tên Là Gì?',
                'answer' => '"Ta là Đấng Tự Hữu Hằng Hữu" (YHWH)',
                'reference' => 'Xuất Ê-díp-tô Ký 3:14',
            ],
            [
                'question' => '9 bông trái của Thánh Linh là gì?',
                'answer' => 'Yêu thương, vui mừng, bình an, nhịn nhục, nhân từ, hiền lành, trung tín, mềm mại, tiết độ.',
                'reference' => 'Ga-la-ti 5:22-23',
            ],
            [
                'question' => 'Vị Hoàng đế La Mã cai trị lúc Chúa Giê-xu giáng sinh tên là gì?',
                'answer' => 'Au-gút-tơ (Caesar Augustus)',
                'reference' => 'Lu-ca 2:1',
            ],
            [
                'question' => 'Ai là tác giả sách Khải Huyền?',
                'answer' => 'Sứ đồ Giăng (nhận khải tượng khi bị đày ở đảo Bát-mô)',
                'reference' => 'Khải Huyền 1:9',
            ],
            [
                'question' => 'Ý nghĩa nguyên ngữ của từ "Phúc Âm" (Gospel) là gì?',
                'answer' => 'Tin Tức Tốt Lành (Good News)',
                'reference' => 'Mác 1:1',
            ],
        ];

        foreach ($cards as $c) {
            BlFlashcard::create([
                'question' => $c['question'],
                'answer' => $c['answer'],
                'reference' => $c['reference'],
                'status' => 'active',
            ]);
        }

        // Bơm 3 thẻ Rác vào bảng Nháp để trang Approval Center có cái mà duyệt
        $temps = [
            ['type' => 'flashcard', 'title' => 'Sự kiện Ghi-đê-ôn', 'status' => 'pending', 'raw_data' => json_encode(['question' => 'Ai đánh dân Ma-đi-an?', 'answer' => 'Ghi-đê-ôn và 300 người'])],
            ['type' => 'event', 'title' => 'Trận lụt Đại Hồng Thủy', 'status' => 'pending', 'raw_data' => json_encode(['question' => 'Ai đóng tàu cứu thế giới?', 'answer' => 'Nô-ê'])],
        ];

        foreach ($temps as $t) {
            BlTempEntity::create($t);
        }
    }
}
