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
        Schema::create('ppt_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('ppt_templates')->cascadeOnDelete();
            $table->float('x');
            $table->float('y');
            $table->float('width');
            $table->float('height');
            $table->json('font_config')->nullable();
            $table->boolean('is_green_screen')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppt_presets');
    }
};
