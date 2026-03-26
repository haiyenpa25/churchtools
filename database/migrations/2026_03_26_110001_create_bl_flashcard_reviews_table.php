<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_flashcard_reviews', function (Blueprint $table) {
            $table->id();
            // Nếu có Auth User thì mở comment dòng dưới:
            // $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flashcard_id')->constrained('bl_flashcards')->cascadeOnDelete();
            $table->float('ease_factor')->default(2.5)->comment('Độ khó của thẻ, bắt đầu từ 2.5');
            $table->integer('interval')->default(0)->comment('Khoảng cách lặp (đơn vị: ngày)');
            $table->dateTime('next_review_date')->comment('Ngày giờ sẽ hiển thị lại cho chu kỳ tiếp theo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_flashcard_reviews');
    }
};
