<?php

namespace Database\Seeders;

use App\Models\BlEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProEventSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        BlEvent::truncate();
        Schema::enableForeignKeyConstraints();

        $events = [
            [
                'title' => 'Tạo dựng Trời và Đất',
                'era' => 'Thời kỳ Khởi Nguyên',
                'description' => 'Đức Chúa Trời phán, mọi sự liền có. Ngài phân rẽ sáng và tối, tạo ra biển, đất, cây cối, thú vật và loài người (A-đam và Ê-va).',
                'image_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80',
                'order_index' => 1,
            ],
            [
                'title' => 'Đại Hồng Thủy & Tàu Nô-ê',
                'era' => 'Khoảng 2348 BC',
                'description' => 'Thế giới ngập tràn tội lỗi, Đức Chúa Trời dùng nước lụt hủy diệt mọi loài, chỉ cứu Nô-ê và gia đình cùng các loài vật trên tàu.',
                'image_url' => 'https://images.unsplash.com/photo-1601398145229-21ea28038d17?auto=format&fit=crop&q=80',
                'order_index' => 2,
            ],
            [
                'title' => 'Chúa gọi Áp-ra-ham',
                'era' => 'Khoảng 1921 BC',
                'description' => 'Chúa gọi Áp-ram rời khởi quê hương (U-rơ) đi đến xứ Canaan. Thiết lập giao ước ngài sẽ làm cho dòng dõi ông đông như sao trên trời.',
                'image_url' => 'https://images.unsplash.com/photo-1621213327685-6187b40dff75?auto=format&fit=crop&q=80',
                'order_index' => 3,
            ],
            [
                'title' => 'Cuộc Xuất Ai Cập (Exodus)',
                'era' => 'Khoảng 1446 BC',
                'description' => 'Môi-se dùng quyền phép Chúa rẽ Biển Đỏ, dẫn dắt dân Y-sơ-ra-ên thoát khỏi ách nô lệ tại Ai Cập.',
                'image_url' => 'https://images.unsplash.com/photo-1444858291040-58f756a3bdd6?auto=format&fit=crop&q=80',
                'order_index' => 4,
            ],
            [
                'title' => 'Chúa Giê-xu Giáng Sinh',
                'era' => 'Khoảng 4 BC',
                'description' => 'Ngôi Hai Thiên Chúa giáng sinh làm người tại chuồng chiên máng cỏ (Bết-lê-hem) để chuẩn bị cho chức vụ cứu chuộc.',
                'image_url' => 'https://images.unsplash.com/photo-1543831628-9f2fb1380922?auto=format&fit=crop&q=80',
                'order_index' => 5,
            ],
        ];

        foreach ($events as $e) {
            BlEvent::create($e);
        }
    }
}
