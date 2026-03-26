<?php

namespace Database\Factories;

use App\Models\PptPreset;
use App\Models\PptTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class PptPresetFactory extends Factory
{
    protected $model = PptPreset::class;

    public function definition(): array
    {
        return [
            'template_id' => PptTemplate::factory(),
            'x' => $this->faker->randomFloat(2, 0, 10),
            'y' => $this->faker->randomFloat(2, 0, 10),
            'width' => $this->faker->randomFloat(2, 2, 8),
            'height' => $this->faker->randomFloat(2, 1, 4),
            'font_config' => ['name' => 'Arial', 'size' => 40, 'color' => '#ffffff'],
            'is_green_screen' => $this->faker->boolean(80),
        ];
    }
}
