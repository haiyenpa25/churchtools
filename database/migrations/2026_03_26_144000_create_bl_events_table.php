<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('era')->nullable()->comment('VD: Khoảng thời gian ước tính (VD: 4000 BC)');
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->integer('order_index')->default(0)->comment('Thứ tự sắp xếp trên Timeline');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_events');
    }
};
