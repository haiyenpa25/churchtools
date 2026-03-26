<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('label')->comment('Tên thực thể (Áp-ra-ham, Bết-lê-hem...)');
            $table->string('group')->default('person')->comment('person, place, event, concept');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bl_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_node_id')->constrained('bl_nodes')->onDelete('cascade');
            $table->foreignId('target_node_id')->constrained('bl_nodes')->onDelete('cascade');
            $table->string('relationship')->comment('Loại quan hệ (VD: is_father_of, happened_at)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_edges');
        Schema::dropIfExists('bl_nodes');
    }
};
