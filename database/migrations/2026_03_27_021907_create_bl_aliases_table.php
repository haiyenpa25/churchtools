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
        Schema::create('bl_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('bl_nodes')->onDelete('cascade');
            $table->string('alias_name')->index()->comment('Bí danh, ví dụ: Si-môn');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_aliases');
    }
};
