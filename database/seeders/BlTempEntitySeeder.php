<?php

namespace Database\Seeders;

use App\Models\BlTempEntity;
use Illuminate\Database\Seeder;

class BlTempEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Xóa dữ liệu cũ nếu có
        BlTempEntity::truncate();

        $data = [
            [
                'type' => 'flashcard',
                'title' => 'Đức Chúa Trời Tên Là Gì?',
                'description' => 'Tìm hiểu danh xưng của Đức Chúa Trời trong Cựu Ước theo nguyên ngữ.',
                'raw_data' => [
                    'question' => 'Danh xưng Giê-hô-va (YHWH) có ý nghĩa gì?',
                    'answer' => '"Ta là Đấng Tự Hữu Hằng Hữu" (I AM WHO I AM).',
                    'reference' => 'Xuất Ê-díp-tô Ký 3:14',
                    'difficulty' => 'easy',
                    'tags' => ['Cựu Ước', 'Thần Học'],
                ],
                'status' => 'pending',
            ],
            [
                'type' => 'event',
                'title' => 'Vượt Biển Đỏ',
                'description' => 'Sự kiện lịch sử giải cứu dân Y-sơ-ra-ên khỏi ách nô lệ Ai Cập.',
                'raw_data' => [
                    'date' => '~ 1446 TCN',
                    'location' => 'Biển Đỏ (Biển Lau Sậy)',
                    'key_figures' => ['Môi-se', 'A-rôn', 'Pha-ra-ôn'],
                    'theological_meaning' => 'Biểu tượng kinh điển của sự cứu rỗi và báp-têm trong Tân Ước (1 Cô 10:1-2).',
                ],
                'status' => 'pending',
            ],
            [
                'type' => 'character',
                'title' => 'Sứ đồ Phao-lô',
                'description' => 'Hồ sơ tóm tắt về vị sứ đồ ngoại bang vĩ đại nhất lịch sử cội nguồn Tân Ước.',
                'raw_data' => [
                    'original_name' => 'Sau-lơ người Táp-sơ',
                    'conversion' => 'Trên đường đến Đam-mách (Công vụ 9)',
                    'authored_books' => 13,
                    'key_trait' => 'Một người Pha-ri-si nhiệt thành được biến đổi thành Sứ đồ tử đạo vì Đấng Christ.',
                ],
                'status' => 'pending',
            ],
            [
                'type' => 'flashcard',
                'title' => 'Quả của Thánh Linh',
                'description' => 'Ghi nhớ 9 danh từ miêu tả bông trái Chúa Thánh Linh.',
                'raw_data' => [
                    'question' => '9 mỹ đức cấu thành Trái của Thánh Linh là gì?',
                    'answer' => 'Yêu thương, vui mừng, bình an, nhịn nhục, nhân từ, hiền lành, trung tín, mềm mại, tiết độ.',
                    'reference' => 'Ga-la-ti 5:22-23',
                    'difficulty' => 'medium',
                ],
                'status' => 'pending',
            ],
            [
                'type' => 'timeline',
                'title' => 'Vương Quốc Chia Đôi',
                'description' => 'Mốc thời gian Y-sơ-ra-ên bị phân liệt thành 2 vương quốc Nam và Bắc.',
                'raw_data' => [
                    'year' => '931 TCN',
                    'northern_kingdom' => 'Y-sơ-ra-ên (10 chi phái) - Vua: Giê-rô-bô-am',
                    'southern_kingdom' => 'Giu-đa (2 chi phái) - Vua: Rô-bô-am',
                    'trigger_event' => 'Sự cai trị khắc nghiệt của vua Rô-bô-am sau khi vua Sa-lô-môn băng hà.',
                ],
                'status' => 'pending',
            ],
        ];

        foreach ($data as $item) {
            BlTempEntity::create($item);
        }
    }
}
