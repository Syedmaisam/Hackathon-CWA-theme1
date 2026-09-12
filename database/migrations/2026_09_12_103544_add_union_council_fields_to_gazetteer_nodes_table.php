<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A union council is a `landmark` node under its town with `uc_code` set.
     * Additive only — `kind` is frozen, so no enum change.
     */
    public function up(): void
    {
        Schema::table('gazetteer_nodes', function (Blueprint $table) {
            $table->string('uc_code')->nullable()->after('kind');
            $table->string('contact_name')->nullable()->after('needs_human_review');
            $table->string('contact_phone')->nullable()->after('contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('gazetteer_nodes', function (Blueprint $table) {
            $table->dropColumn(['uc_code', 'contact_name', 'contact_phone']);
        });
    }
};
