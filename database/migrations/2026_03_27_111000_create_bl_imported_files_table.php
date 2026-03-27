<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bl_imported_files', function (Blueprint $table) {
            $table->id();
            $table->string('category')->comment('Thư mục lưu trữ: kinh-thanh, giai-nghia, duong-linh');
            $table->string('file_name')->comment('Tên file gốc');
            $table->string('file_hash')->unique()->comment('Mã MD5 của nội dung file để chống sửa lén');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('total_chunks')->default(0);
            $table->integer('processed_chunks')->default(0);
            $table->integer('nodes_added')->default(0);
            $table->integer('edges_added')->default(0);
            $table->text('error_log')->nullable();
            $table->timestamps();
            
            // Đảm bảo không nạp lặp 1 file trong 1 thư mục trừ khi file bị sửa content (khác hash)
            $table->index(['category', 'file_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bl_imported_files');
    }
};
