<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_terms_and_conditions', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('terms_and_condition_id');
            $table->unique(['service_id', 'terms_and_condition_id'], 'stc_unique');
            $table->index('service_id');
            $table->index('terms_and_condition_id');
        });

        // Backfill from existing terms_and_conditions.service_id
        if (Schema::hasTable('terms_and_conditions')) {
            $rows = DB::table('terms_and_conditions')
                ->select('id as terms_and_condition_id', 'service_id')
                ->whereNotNull('service_id')
                ->get();
            foreach ($rows as $r) {
                DB::table('service_terms_and_conditions')->updateOrInsert([
                    'service_id' => $r->service_id,
                    'terms_and_condition_id' => $r->terms_and_condition_id,
                ], []);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_terms_and_conditions');
    }
};

