<?php

namespace Database\Factories;

use App\Models\PptTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class PptTemplateFactory extends Factory
{
    protected $model = PptTemplate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'file_path' => 'templates/sample_'.$this->faker->uuid.'.pptx',
            'status' => 'active',
        ];
    }
}
