<?php

namespace Database\Seeders;

use App\Models\PptTemplate;
use Illuminate\Database\Seeder;

class BannerTemplatesSeeder extends Seeder
{
    /**
     * All 10 banner template designs.
     * font_config stores both typography AND visual theme colors:
     *   banner_color, logo_border_color, logo_bg_color, accent_color
     * These are read by the frontend WYSIWYG preview (dynamic CSS)
     * and by ppt_generator.py for slide background effects.
     */
    public function run(): void
    {
        $templates = [
            // ── 1 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Teal Gold Classic',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '004D40',
                    'logo_border_color' => 'FFD700',
                    'logo_bg_color' => '004D40',
                    'accent_color' => 'FFD700',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'FFD700',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
            // ── 2 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Midnight Navy Royal',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '0D1B2A',
                    'logo_border_color' => 'E0E8F0',
                    'logo_bg_color' => '1A2E42',
                    'accent_color' => '90CAF9',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'FFFFFF',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
            // ── 3 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Crimson Sanctuary',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '6B0020',
                    'logo_border_color' => 'FFE082',
                    'logo_bg_color' => '8B0030',
                    'accent_color' => 'FF8A80',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'FFE082',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
            // ── 4 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Forest Cathedral',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '1B4332',
                    'logo_border_color' => 'D4EDDA',
                    'logo_bg_color' => '1B4332',
                    'accent_color' => '81C784',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'F0FFF0',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
            // ── 5 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Royal Amethyst',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '2D1B69',
                    'logo_border_color' => 'CE93D8',
                    'logo_bg_color' => '3D2B89',
                    'accent_color' => 'CE93D8',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'EDE7F6',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
            // ── 6 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Charcoal Modern',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '1A1A2E',
                    'logo_border_color' => '00E5FF',
                    'logo_bg_color' => '16213E',
                    'accent_color' => '00BCD4',
                    'name' => 'Arial',
                    'size' => 36,
                    'color' => '00E5FF',
                    'shadow' => true,
                    'stroke' => false,
                ],
            ],
            // ── 7 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Burnt Sienna Warm',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '2C0E0E',
                    'logo_border_color' => 'FFCCBC',
                    'logo_bg_color' => '3E1A1A',
                    'accent_color' => 'FF7043',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'FFCCBC',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
            // ── 8 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Deep Ocean Blue',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '003554',
                    'logo_border_color' => 'B3E5FC',
                    'logo_bg_color' => '004D78',
                    'accent_color' => '29B6F6',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'B3E5FC',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
            // ── 9 ──────────────────────────────────────────────────────────────
            [
                'name' => 'Onyx Premium',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '0D0D0D',
                    'logo_border_color' => 'FFD700',
                    'logo_bg_color' => '1A1A1A',
                    'accent_color' => 'BDBDBD',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'FFFFFF',
                    'shadow' => true,
                    'stroke' => false,
                ],
            ],
            // ── 10 ─────────────────────────────────────────────────────────────
            [
                'name' => 'Amber Harvest',
                'file_path' => storage_path('app/public/templates/thanh_ca_teal.pptx'),
                'preset' => [
                    'banner_color' => '3E1F00',
                    'logo_border_color' => 'FFA726',
                    'logo_bg_color' => '5D2E00',
                    'accent_color' => 'FF8F00',
                    'name' => 'Georgia',
                    'size' => 36,
                    'color' => 'FFD700',
                    'shadow' => true,
                    'stroke' => true,
                ],
            ],
        ];

        foreach ($templates as $data) {
            $preset = $data['preset'];

            $template = PptTemplate::updateOrCreate(
                ['name' => $data['name']],
                ['file_path' => $data['file_path']]
            );

            $template->presets()->delete();

            $template->presets()->create([
                'x' => 0,     // Layout engine handles coordinates
                'y' => 0,
                'width' => 0,
                'height' => 0,
                'font_config' => json_encode($preset),
                'is_green_screen' => true,
            ]);
        }
    }
}
