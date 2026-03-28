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
        Schema::create('bible_commentaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_book_id')->constrained('bible_books')->cascadeOnDelete();
            
            // To store the numeric references if possible
            $table->integer('chapter_start')->nullable();
            $table->integer('verse_start')->nullable();
            $table->integer('chapter_end')->nullable();
            $table->integer('verse_end')->nullable();
            
            // Raw text for reference (e.g. "Sa 1:1-31")
            $table->string('reference_string')->nullable();
            $table->text('title')->nullable(); // Doi thanh text
            
            $table->longText('content')->nullable();
            $table->json('raw_data')->nullable(); // Thêm cột này
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_commentaries');
    }
};
