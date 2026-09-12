<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gazetteer_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('aliases')->nullable();
            $table->enum('kind', ['district', 'town', 'special_zone', 'landmark']);
            $table->foreignId('parent_id')->nullable()->constrained('gazetteer_nodes')->nullOnDelete();
            $table->string('district')->nullable();
            $table->string('tmc_authority_id')->nullable();
            $table->foreign('tmc_authority_id')->references('id')->on('authorities')->nullOnDelete();
            $table->string('special_zone_authority_id')->nullable();
            $table->foreign('special_zone_authority_id')->references('id')->on('authorities')->nullOnDelete();
            $table->boolean('needs_human_review')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gazetteer_nodes');
    }
};
