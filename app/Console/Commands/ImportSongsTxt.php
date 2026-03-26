<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;

class ImportSongsTxt extends Command
{
    protected $signature = 'songs:import';

    protected $description = 'Import songs from txt files in trinh-chieu directory mapped to categories';

    public function handle()
    {
        $baseDir = base_path('trinh-chieu');
        $dirs = ['thanh ca tin lanh', 'ton vinh chua hang huu'];

        $count = 0;
        foreach ($dirs as $dir) {
            $fullPath = $baseDir.DIRECTORY_SEPARATOR.$dir;
            if (! is_dir($fullPath)) {
                continue;
            }

            $files = glob($fullPath.DIRECTORY_SEPARATOR.'*.txt');
            foreach ($files as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);

                $number = null;
                $title = $filename;
                // Parse "001 - Title" or "1. Title"
                if (preg_match('/^(\d+)\s*[-.]\s*(.*)$/', $filename, $matches)) {
                    $number = $matches[1];
                    $title = $matches[2];
                }

                $lyrics = file_get_contents($file);

                Song::updateOrCreate(
                    ['title' => $title, 'category' => $dir],
                    ['number' => $number, 'lyrics' => $lyrics]
                );
                $count++;
            }
        }

        $this->info("Imported $count songs successfully.");
    }
}
