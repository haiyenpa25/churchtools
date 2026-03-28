<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Log;

class MemoryDumperService
{
    /**
     * Dumps in-memory graph data to a JSON file safely.
     */
    public function dumpToJsonFile(string $bookName, array $nodes, array $edges): void
    {
        $payload = [
            'book' => $bookName,
            'exported_at' => now()->toIso8601String(),
            'total_nodes' => count($nodes),
            'total_edges' => count($edges),
            'nodes' => $nodes,
            'edges' => $edges,
        ];

        // Format filename cực sạch, đè File cũ nếu chạy lại
        $fileName = 'ollama_graph_'.$bookName.'.json';

        // Write file thẳng hướng đường dẫn /database/data/bible_dump/
        $fullPath = database_path('data/bible_dump/'.$fileName);

        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Ép kiểu Data lớn bằng Chunk String nén
        file_put_contents($fullPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        Log::info("Memory Dumper: Đã xả đống Rác siêu lớn từ RAM -> File $fileName (".count($nodes).' Nodes)');
    }
}
