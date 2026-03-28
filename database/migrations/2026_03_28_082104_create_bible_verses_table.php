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
        Schema::create('bible_verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_chapter_id')->constrained('bible_chapters')->onDelete('cascade');
            $table->integer('verse_number');
            $table->text('content');
            $table->timestamps();

            $table->unique(['bible_chapter_id', 'verse_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_verses');
    }
};
