<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportSongsToJsonCommand extends Command
{
    protected $signature = 'songs:export-json';

    protected $description = 'Export all songs from MySQL database into a portable JSON Foundation Data';

    public function handle()
    {
        $this->info('Starting Songs JSON Export...');

        $songs = Song::all();

        $destDir = database_path('data/foundation');
        if (! File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $destFile = $destDir.'/songs.json';
        File::put($destFile, json_encode($songs->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('Exported '.$songs->count()." songs from database to $destFile");

        return 0;
    }
}
