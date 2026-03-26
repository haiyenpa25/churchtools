<?php

namespace Database\Seeders;

use App\Models\BlEvent;
use App\Models\BlSubject;
use Illuminate\Database\Seeder;

class BlEventSeeder extends Seeder
{
    public function run(): void
    {
        $subject = BlSubject::where('name', 'Đoạn 1')->first();

        BlEvent::create([
            'title' => 'Dân Y-sơ-ra-ên hỏi Đức Chúa Trời',
            'description' => 'Sau khi Giô-suê qua đời, dân Y-sơ-ra-ên cầu hỏi Đức Giê-hô-va xem ai sẽ đi lên đánh dân Ca-na-an trước hết.',
            'subject_id' => $subject ? $subject->id : null,
            'era' => 'Thời Các Quan Xét',
            'start_year' => 1370,
            'end_year' => 1370,
        ]);
    }
}
