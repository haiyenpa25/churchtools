<?php

namespace Database\Seeders;

use App\Models\PptPreset;
use App\Models\PptTemplate;
use Illuminate\Database\Seeder;

class PptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        PptTemplate::factory(5)
            ->has(PptPreset::factory()->count(2), 'presets')
            ->create();
    }
}
