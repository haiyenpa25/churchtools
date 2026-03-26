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
        Schema::create('bl_entity_events', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained('bl_events')->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('bl_entities')->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->timestamps();

            $table->primary(['event_id', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bl_entity_events');
    }
};
