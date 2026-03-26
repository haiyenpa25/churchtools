<?php

namespace Database\Seeders;

use App\Models\PptTemplate;
use Illuminate\Database\Seeder;

class TealTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $template = PptTemplate::updateOrCreate(
            ['name' => 'Hát Thánh Ca Livestream (Xanh ngọc)'],
            [
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
            ]
        );

        $template->presets()->delete();

        $template->presets()->create([
            'x' => 1.82,      // TEXT_X = LOGO_X + LOGO_SIZE + 0.1 = 0.12 + 1.5996 + 0.1
            'y' => 6.375,     // TEXT_Y = SLIDE_H - BANNER_H = 7.5 - 1.125  ← BOTTOM ANCHORED
            'width' => 11.31, // TEXT_W = SLIDE_W - TEXT_X - 0.1
            'height' => 1.125, // TEXT_H = BANNER_H = SLIDE_H * 0.15
            'font_config' => json_encode([
                'name' => 'Georgia',
                'size' => 36,         // Intentionally lower so TEXT_TO_FIT_SHAPE has room
                'color' => 'FFD700',
                'shadow' => true,
                'stroke' => true,
            ]),
            'is_green_screen' => true,
        ]);
    }
}
