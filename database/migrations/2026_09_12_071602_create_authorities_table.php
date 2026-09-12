<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorities', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->enum('kind', ['citywide', 'tmc', 'cantonment', 'estate', 'fallback']);
            $table->string('district')->nullable();
            $table->string('website')->nullable();
            $table->boolean('website_verified')->default(false);
            $table->string('email')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('phone')->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->string('secondary_phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('citizen_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorities');
    }
};
