<?php

namespace Database\Seeders;

use App\Models\BlSubject;
use Illuminate\Database\Seeder;

class BlSubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Example seeder for Judges
        $book = BlSubject::create([
            'name' => 'Các Quan Xét',
            'type' => 'book',
        ]);

        BlSubject::create([
            'parent_id' => $book->id,
            'name' => 'Đoạn 1',
            'type' => 'chapter',
        ]);

        BlSubject::create([
            'parent_id' => $book->id,
            'name' => 'Đoạn 2',
            'type' => 'chapter',
        ]);
    }
}
