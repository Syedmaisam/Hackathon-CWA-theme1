<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routing_rules', function (Blueprint $table) {
            $table->string('issue_type')->primary();
            $table->string('primary_authority_id');
            $table->foreign('primary_authority_id')->references('id')->on('authorities');
            $table->json('co_authority_ids')->nullable();
            $table->boolean('internal_street_goes_to_tmc')->default(false);
            $table->json('flags')->nullable();
            $table->text('rule_note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routing_rules');
    }
};
