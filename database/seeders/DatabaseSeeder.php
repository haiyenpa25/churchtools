<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'haiyenpa25'],
            [
                'name' => 'Hải Yến',
                'password' => bcrypt('Haiyen@2026'),
            ]
        );

        $this->call([
            PptTemplateSeeder::class,
            FinanceSeeder::class,
        ]);
    }
}
