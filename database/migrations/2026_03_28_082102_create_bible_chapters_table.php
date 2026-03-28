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
        Schema::create('bible_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_book_id')->constrained('bible_books')->onDelete('cascade');
            $table->integer('chapter_number');
            $table->integer('verse_count')->default(0);
            $table->timestamps();

            $table->unique(['bible_book_id', 'chapter_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_chapters');
    }
};
