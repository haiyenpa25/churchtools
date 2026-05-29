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
        Schema::create('finance_debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('partner_name', 100);
            $table->enum('type', ['borrow', 'lend']);
            $table->decimal('amount', 15, 2);
            $table->date('due_date')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'partially_paid'])->default('unpaid');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_debts');
    }
};
