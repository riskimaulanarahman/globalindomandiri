<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'terms_and_conditions_id')) {
                $table->unsignedBigInteger('terms_and_conditions_id')->nullable()->after('service_id');
                $table->index('terms_and_conditions_id');
            }
        });

        // Backfill: link to the first active T&C of the selected service if snapshot exists
        if (Schema::hasTable('terms_and_conditions')) {
            DB::statement(
                "UPDATE quotations q SET terms_and_conditions_id = (
                    SELECT t.id FROM terms_and_conditions t
                    WHERE t.service_id = q.service_id AND t.is_active = 1
                    ORDER BY t.id LIMIT 1
                )
                WHERE q.terms_and_conditions_id IS NULL
                  AND q.service_id IS NOT NULL
                  AND q.terms_conditions IS NOT NULL"
            );
        }
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'terms_and_conditions_id')) {
                $table->dropIndex(['terms_and_conditions_id']);
                $table->dropColumn('terms_and_conditions_id');
            }
        });
    }
};

