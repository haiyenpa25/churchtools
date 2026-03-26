<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_quizzes', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->json('options')->comment('Mảng 4 đáp án');
            $table->string('correct_option')->comment('A, B, C, D');
            $table->text('explanation')->nullable()->comment('Giải thích lý do tại sao đúng');
            $table->string('reference')->nullable()->comment('Kinh thánh tham chiếu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_quizzes');
    }
};
