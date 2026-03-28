<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportBibleToJsonCommand extends Command
{
    protected $signature = 'bible:export-json';

    protected $description = 'Export all Bible txt files into a single structured JSON Foundation Data';

    public function handle()
    {
        $this->info('Starting Bible JSON Export...');

        $sourceDir = base_path('tai-lieu/kinh-thanh');
        if (! File::exists($sourceDir)) {
            $this->error("Directory not found: $sourceDir");

            return 1;
        }

        $files = File::files($sourceDir);
        $books = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            // Expected format: 01_Sang-the-ky_1.txt or 46_I Co-rinh-to_1.txt
            if (! preg_match('/^(\d+_[A-Za-z0-9\-\s]+)_(\d+)\.txt$/', $filename, $matches)) {
                $this->warn("Skipped unmatchable file: $filename");

                continue;
            }

            $bookKey = $matches[1];
            $chapterNumber = (int) $matches[2];

            $content = file_get_contents($file->getRealPath());
            $lines = explode("\n", str_replace("\r", '', $content));

            $chapterTitle = trim(array_shift($lines)); // first line is title

            $verses = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                // Parse verse number and text
                if (preg_match('/^(\d+)\s+(.+)$/', $line, $vMatches)) {
                    $verses[] = [
                        'verseNumber' => (int) $vMatches[1],
                        'content' => trim($vMatches[2]),
                    ];
                }
            }

            if (! isset($books[$bookKey])) {
                $books[$bookKey] = [
                    'book' => $chapterTitle ? preg_replace('/\s+\d+$/', '', $chapterTitle) : $bookKey,
                    'book_key' => $bookKey,
                    'chapters' => [],
                ];
            }

            $books[$bookKey]['chapters'][] = [
                'chapter' => $chapterNumber,
                'title' => $chapterTitle,
                'verseCount' => count($verses),
                'verses' => $verses,
            ];
        }

        // Sort books by key
        uksort($books, function ($a, $b) {
            // "01_Sang-the-ky" -> extract prefix 01
            $numA = (int) preg_replace('/[^0-9].*$/', '', $a);
            $numB = (int) preg_replace('/[^0-9].*$/', '', $b);

            return $numA <=> $numB;
        });

        // Sort chapters within books
        foreach ($books as &$book) {
            usort($book['chapters'], function ($a, $b) {
                return $a['chapter'] <=> $b['chapter'];
            });
        }

        // Flatten books array
        $finalData = array_values($books);

        $destDir = database_path('data/foundation');
        if (! File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $destFile = $destDir.'/bible.json';
        File::put($destFile, json_encode($finalData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('Exported '.count($finalData)." books to $destFile");

        return 0;
    }
}
