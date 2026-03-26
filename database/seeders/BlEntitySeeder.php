<?php

namespace Database\Seeders;

use App\Models\BlEntity;
use Illuminate\Database\Seeder;

class BlEntitySeeder extends Seeder
{
    public function run(): void
    {
        BlEntity::create([
            'name' => 'Ghi-đê-ôn',
            'entity_type' => 'character',
            'metadata' => [
                'meaning_of_name' => 'Kẻ đốn cây / Kẻ hủy diệt',
                'background' => 'Thuộc chi phái Ma-na-se, gia đình nghèo hèn nhất.',
            ],
        ]);

        BlEntity::create([
            'name' => 'Suối Ha-rốt',
            'entity_type' => 'location',
            'metadata' => [
                'lat' => 32.551,
                'lng' => 35.353,
                'description' => 'Nơi Ghi-đê-ôn đóng quân và thử thách 300 người.',
            ],
        ]);
    }
}
