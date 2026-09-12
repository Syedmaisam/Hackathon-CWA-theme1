<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gates the Filament panel. Without this, the moment citizen registration
     * exists every registered citizen can reach /admin and edit routing rules
     * or delete reports — the panel has no other access check.
     *
     * Defaults to false so any account created later is a citizen unless
     * something deliberately promotes it.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
