<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportGraphCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bible:import-dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đọc hàng loạt file JSON trong thư mục bible_dump để nạp thẳng vào CSDL (Không qua AI)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dumpDir = database_path('data/bible_dump');

        if (!File::exists($dumpDir)) {
            $this->error("Không tìm thấy thư mục dump: {$dumpDir}");
            $this->line("Vui lòng copy thư mục 'bible_dump' từ môi trường Local lên server.");
            return 1;
        }

        $this->warn('⚠️  Lệnh này sẽ nạp dữ liệu đè lên Database. Đang chạy...');

        // Đọc tất cả các file JSON
        $files = File::files($dumpDir);
        $nodeFiles = [];
        $edgeFiles = [];

        foreach ($files as $file) {
            if (str_starts_with($file->getFilename(), 'nodes_')) {
                $nodeFiles[] = $file;
            } elseif (str_starts_with($file->getFilename(), 'edges_')) {
                $edgeFiles[] = $file;
            }
        }

        if (empty($nodeFiles) && empty($edgeFiles)) {
            $this->error('Không có file JSON nào trong thư mục dump!');
            return 1;
        }

        $this->info("Tìm thấy " . count($nodeFiles) . " file Nodes và " . count($edgeFiles) . " file Edges.");

        DB::beginTransaction();
        try {
            // NẠP NODES TRƯỚC (Vì Edges yêu cầu Khóa ngoại trỏ đến Node)
            foreach ($nodeFiles as $file) {
                $this->line("Đang nạp file Thực Thể: {$file->getFilename()}...");
                $json = File::get($file->getPathname());
                $data = json_decode($json, true);
                
                // Ép Ký tự Unicode và Encode mảng JSON cho cột metadata nếu cần
                // Eloquent toArray() đã trả về array cho attribute casting. 
                // Khi dùng DB::table()->insert(), ta phải tự encode mảng metadata lại thành string JSON.
                foreach ($data as &$row) {
                    if (isset($row['metadata']) && is_array($row['metadata'])) {
                        $row['metadata'] = json_encode($row['metadata'], JSON_UNESCAPED_UNICODE);
                    }
                }

                // Chèn với tốc độ cao (Bulk Insert), bỏ qua ID trùng lặp
                DB::table('bl_nodes')->insertOrIgnore($data);
            }

            // NẠP EDGES
            foreach ($edgeFiles as $file) {
                $this->line("Đang nạp file Mối Quan Hệ: {$file->getFilename()}...");
                $json = File::get($file->getPathname());
                $data = json_decode($json, true);

                foreach ($data as &$row) {
                    if (isset($row['metadata']) && is_array($row['metadata'])) {
                        $row['metadata'] = json_encode($row['metadata'], JSON_UNESCAPED_UNICODE);
                    }
                }

                DB::table('bl_edges')->insertOrIgnore($data);
            }

            DB::commit();
            $this->info('✅ NẠP DỮ LIỆU HOÀN TẤT THÀNH CÔNG RỰC RỠ!');
            $this->line('Kiến trúc Graph Database của bạn hiện đã được đồng bộ với Local.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Có lỗi xảy ra trong quá trình nạp dữ liệu: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
