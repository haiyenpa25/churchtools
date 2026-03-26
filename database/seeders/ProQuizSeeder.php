<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlQuiz;

class ProQuizSeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        BlQuiz::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $quizzes = [
            [
                'question' => 'Đức Chúa Trời nhậm xác người và bị giới hạn trong không gian thời gian ở hình hài nào?',
                'options' => [
                    'A' => 'Thiên sứ trưởng Mi-ca-ên',
                    'B' => 'Vua Đa-vít hiển vinh',
                    'C' => 'Chúa Giê-xu Christ',
                    'D' => 'Thần Chân lý linh cảm'
                ],
                'correct_option' => 'C',
                'explanation' => 'Chúa Giê-xu Christ là Ngôi Hai Thiên Chúa đã giáng thế làm người, "Đạo trở nên xác thịt" nhằm mục đích cứu chuộc nhân loại.',
                'reference' => 'Giăng 1:14'
            ],
            [
                'question' => 'Ai là người đã bán rẻ quyền trưởng nam của mình chỉ vì một bát canh đậu đỏ?',
                'options' => [
                    'A' => 'Ê-sau',
                    'B' => 'Gia-cốp',
                    'C' => 'Ca-in',
                    'D' => 'Ru-bên'
                ],
                'correct_option' => 'A',
                'explanation' => 'Ê-sau coi thường đặc quyền trưởng nam, bán nó cho người em sinh đôi là Gia-cốp chỉ để đổi lấy bữa ăn khi đi săn về.',
                'reference' => 'Sáng Thế Ký 25:29-34'
            ],
            [
                'question' => 'Vị tiên tri nào bị cá lớn nuốt vào bụng trong 3 ngày 3 đêm vì trốn tránh mệnh lệnh của Chúa đi đến Ni-ni-ve?',
                'options' => [
                    'A' => 'Ê-li',
                    'B' => 'Giô-na',
                    'C' => 'Giê-rê-mi',
                    'D' => 'Ê-sai'
                ],
                'correct_option' => 'B',
                'explanation' => 'Giô-na bất tuân lệnh Chúa bắt tàu đi sang hướng ngược lại (Ta-rê-si), nên Chúa đã sắp đặt cá lớn nuốt ông để ông suy ngẫm và an năn.',
                'reference' => 'Giô-na 1'
            ],
            [
                'question' => 'Trái thánh linh gồm bao nhiêu phẩm chất?',
                'options' => [
                    'A' => '7 phẩm chất',
                    'B' => '9 phẩm chất',
                    'C' => '10 phẩm chất',
                    'D' => '12 phẩm chất'
                ],
                'correct_option' => 'B',
                'explanation' => 'Trái Thánh Linh gồm 9 mỹ đức: yêu thương, vui mừng, bình an, nhịn nhục, nhân từ, hiền lành, trung tín, mềm mại, tiết độ.',
                'reference' => 'Ga-la-ti 5:22-23'
            ],
            [
                'question' => 'Chúa Giê-xu tuyên bố điều gì khi hóa bánh ra nhiều nuôi 5000 người ăn?',
                'options' => [
                    'A' => 'Ta là nước Hằng Sống',
                    'B' => 'Ta là con đường, chân lý, và sự sống',
                    'C' => 'Ta là Vua dân Giu-đa',
                    'D' => 'Ta là bánh của sự sống'
                ],
                'correct_option' => 'D',
                'explanation' => 'Chúa dùng bức tranh thuộc thể (Bánh hóa ra nhiều) để minh họa một chân lý thuộc linh sâu sắc: Ngài chính là Bánh Hằng Sống cung ứng cho linh hồn thỏa mãn đời đời.',
                'reference' => 'Giăng 6:35'
            ]
        ];

        foreach($quizzes as $q) {
            BlQuiz::create($q);
        }
    }
}
