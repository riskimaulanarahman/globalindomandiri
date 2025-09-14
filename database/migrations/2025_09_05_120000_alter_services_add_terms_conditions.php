<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('default_terms');
            }
        });
        // Backfill from default_terms if terms_conditions is null
        DB::statement('UPDATE services SET terms_conditions = COALESCE(terms_conditions, default_terms)');
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'terms_conditions')) {
                $table->dropColumn('terms_conditions');
            }
        });
    }
};

