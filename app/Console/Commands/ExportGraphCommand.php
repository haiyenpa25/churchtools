<?php

namespace App\Console\Commands;

use App\Models\BlEdge;
use App\Models\BlNode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportGraphCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bible:export-dump {--chunk=5000 : Số lượng dòng mỗi file JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xuất toàn bộ dữ liệu CSDL Graph ra các file JSON nhỏ để import trực tiếp lên Server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');
        $dumpDir = database_path('data/bible_dump');

        $this->info("Bắt đầu quy trình xuất dữ liệu Graph ra file (Chia nhỏ {$chunkSize} dòng/file)...");

        // Xóa thư mục cũ để tạo mới
        if (File::exists($dumpDir)) {
            File::deleteDirectory($dumpDir);
        }
        File::makeDirectory($dumpDir, 0755, true);

        // 1. XUẤT NODES
        $nodeCount = BlNode::count();
        $this->info("Đang xử lý xuất {$nodeCount} Thực Thể (Nodes)...");

        $nodeChunkIndex = 1;
        BlNode::orderBy('id')->chunk($chunkSize, function ($nodes) use (&$nodeChunkIndex, $dumpDir) {
            $data = $nodes->toArray();
            $filename = "nodes_part_{$nodeChunkIndex}.json";
            File::put("{$dumpDir}/{$filename}", json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $this->line(" - Đã lưu {$filename} (".count($data).' records)');
            $nodeChunkIndex++;
        });

        // 2. XUẤT EDGES
        $edgeCount = BlEdge::count();
        $this->info("Đang xử lý xuất {$edgeCount} Mối Quan Hệ (Edges)...");

        $edgeChunkIndex = 1;
        BlEdge::orderBy('id')->chunk($chunkSize, function ($edges) use (&$edgeChunkIndex, $dumpDir) {
            $data = $edges->toArray();
            $filename = "edges_part_{$edgeChunkIndex}.json";
            File::put("{$dumpDir}/{$filename}", json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $this->line(" - Đã lưu {$filename} (".count($data).' records)');
            $edgeChunkIndex++;
        });

        $this->info("✅ HOÀN TẤT DUMP! Toàn bộ file đã được lưu tại: {$dumpDir}");
        $this->warn('👉 Bạn hãy commit thư mục [database/data/bible_dump] lên Git, sau đó pull về Server production và chạy lệnh: php artisan bible:import-dump');

        return 0;
    }
}
