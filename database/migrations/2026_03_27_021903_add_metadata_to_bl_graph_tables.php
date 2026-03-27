<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bl_nodes', function (Blueprint $table) {
            if (!Schema::hasColumn('bl_nodes', 'metadata')) {
                $table->json('metadata')->nullable()->comment('Lưu thông tin ngữ cảnh mở rộng');
            }
        });

        Schema::table('bl_edges', function (Blueprint $table) {
            if (!Schema::hasColumn('bl_edges', 'metadata')) {
                $table->json('metadata')->nullable()->comment('Lưu tham chiếu: ví dụ {"source_verse": "Sa 1:1"}');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bl_nodes', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });

        Schema::table('bl_edges', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
