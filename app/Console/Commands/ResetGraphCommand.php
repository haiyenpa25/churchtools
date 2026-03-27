<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetGraphCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bible:reset {--force : Thực thi không cần xác nhận (dùng cho mội trường tự động)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa sạch toàn bộ dữ liệu trong bảng bl_nodes và bl_edges để nạp lại từ đầu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('⚠️  CẢNH BÁO: Lệnh này sẽ XÓA TOÀN BỘ dữ liệu Knowledge Graph (Nodes & Edges).');
        
        if (!$this->option('force') && !$this->confirm('Bạn có CỰC KỲ CHẮC CHẮN muốn xóa sạch dữ liệu không?', false)) {
            $this->info('Đã hủy thao tác xóa.');
            return 0;
        }

        $this->info('Đang xóa dữ liệu Graph...');

        // Tắt kiểm tra khóa ngoại để Truncate an toàn
        Schema::disableForeignKeyConstraints();
        
        DB::table('bl_edges')->truncate();
        DB::table('bl_nodes')->truncate();
        
        Schema::enableForeignKeyConstraints();

        $this->info('✅ Đã dọn sạch bảng bl_nodes và bl_edges thành công!');
        
        return 0;
    }
}
