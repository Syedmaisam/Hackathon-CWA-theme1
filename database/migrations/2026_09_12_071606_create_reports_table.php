<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('citizen_name')->nullable();
            $table->string('citizen_phone')->nullable();
            $table->enum('input_mode', ['text', 'voice', 'photo']);
            $table->string('input_language')->nullable();
            $table->text('raw_text');
            $table->string('photo_path')->nullable();
            $table->dateTime('observed_at')->nullable();
            $table->json('classification')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('severity')->nullable();
            $table->json('hazards')->nullable();
            $table->string('location_text')->nullable();
            $table->foreignId('gazetteer_node_id')->nullable()->constrained('gazetteer_nodes')->nullOnDelete();
            $table->string('resolved_area')->nullable();
            $table->string('resolved_special_zone')->nullable();
            $table->json('routing')->nullable();
            $table->string('routing_confidence')->nullable();
            $table->json('routing_flags')->nullable();
            $table->string('clarifying_question')->nullable();
            $table->string('clarifying_answer')->nullable();
            $table->text('draft_en')->nullable();
            $table->text('draft_ur')->nullable();
            $table->string('requested_remedy')->nullable();
            $table->string('status')->default('pending');
            $table->dateTime('ai_failed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
