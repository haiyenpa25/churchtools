<?php

namespace Database\Seeders;

use App\Models\BlTempEntity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TempEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        BlTempEntity::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Giả lập một thẻ Flashcard bóc từ Bài giảng HTTLVN
        BlTempEntity::create([
            'type' => 'flashcard',
            'title' => 'Flashcard Nháp: Cựu Ước có mấy sách tĩnh từ?',
            'description' => 'Trích xuất Flashcard từ Web httlvn.org lúc 10:00 sáng nay bởi Gemini AI.',
            'raw_data' => [
                'question' => 'Năm sách thi ca của Cựu Ước (Gióp, Thi Thiên, Châm Ngôn, Truyền Đạo, Nhã Ca) thuộc thể loại sách gì?',
                'answer' => 'Sách Tĩnh Từ (Hoặc Thi Ca).',
                'tags' => ['Cuu Uoc', 'Van Hoc'],
                'reference' => 'Sổ Tay Kinh Thánh',
            ],
            'status' => 'pending',
        ]);

        // 2. Giả lập một câu Quiz Cực Khó do AI tự bóc
        BlTempEntity::create([
            'type' => 'quiz',
            'title' => 'Quiz Nháp: Ai là người không nếm sự chết?',
            'description' => 'Câu trắc nghiệm do AI thu thập từ kho VietChristian.',
            'raw_data' => [
                'question' => 'Có 2 nhân vật trong Cựu Ước không nếm sự chết mà được cất thẳng lên trời, đó là ai?',
                'options' => [
                    'A' => 'Áp-ra-ham và Môi-se',
                    'B' => 'Hê-nóc và Ê-li',
                    'C' => 'Đa-vít và Sa-lô-môn',
                    'D' => 'Ê-xê-chi-ên và Đa-ni-ên',
                ],
                'correct_option' => 'B',
                'explanation' => 'Hê-nóc đồng đi cùng Đức Chúa Trời rồi mất biệt (Sáng 5:24) và Tiên tri Ê-li được lên trời bằng xe lửa và ngựa lửa (2 Các Vua 2:11).',
                'reference' => 'Sáng 5:24; 2 Các Vua 2:11',
            ],
            'status' => 'pending',
        ]);

        // 3. Giả lập AI tìm thấy Thực Thể mới (Gia-cốp) cho Lưới RAG Graph
        BlTempEntity::create([
            'type' => 'node',
            'title' => 'Node Nháp: Nhân Vật Gia Cốp',
            'description' => 'Node thực thể AI dò thấy từ Lịch Sử Y-sơ-ra-ên.',
            'raw_data' => [
                'id' => 9006,
                'label' => 'Gia-cốp',
                'group' => 'person',
                'description' => 'Con trai Y-sác, sinh ra 12 chi phái Y-sơ-ra-ên. Tổ phụ của tuyển dân.',
            ],
            'status' => 'pending',
        ]);

        // 4. Giả lập liên kết Gia Cốp -> Y-sác
        BlTempEntity::create([
            'type' => 'edge',
            'title' => 'Link Nháp: Gia-cốp là con của Y-sác',
            'description' => 'Cạnh liên kết AI phát hiện thông qua Cây Phả Hệ.',
            'raw_data' => [
                'source_node_id' => 9006, // Gia-cốp
                'target_node_id' => 9005, // Y-sác (nếu ID Y-sác được tạo)
                'relationship' => 'Con trai của',
            ],
            'status' => 'pending',
        ]);
    }
}
