<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE terms_and_conditions MODIFY service_id BIGINT UNSIGNED NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE terms_and_conditions ALTER COLUMN service_id DROP NOT NULL');
        } else {
            // Fallback: attempt using Schema builder (may require doctrine/dbal on some drivers)
            try {
                Schema::table('terms_and_conditions', function (Blueprint $table) {
                    $table->unsignedBigInteger('service_id')->nullable();
                });
            } catch (\Throwable $e) {
                // As a last resort, ignore if driver does not support altering nullability easily
            }
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE terms_and_conditions MODIFY service_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE terms_and_conditions ALTER COLUMN service_id SET NOT NULL');
        } else {
            try {
                Schema::table('terms_and_conditions', function (Blueprint $table) {
                    $table->unsignedBigInteger('service_id');
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
};

