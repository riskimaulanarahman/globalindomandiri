<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms_and_conditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('title', 150);
            $table->text('body');
            $table->string('version', 50)->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_id','is_active']);
        });

        // Backfill: create 1 default T&C per service using existing service terms/default_terms if available
        if (Schema::hasTable('services')) {
            $services = DB::table('services')->select('id', 'name', 'terms_conditions', 'default_terms')->get();
            foreach ($services as $svc) {
                $body = $svc->terms_conditions ?? $svc->default_terms ?? null;
                if ($body) {
                    DB::table('terms_and_conditions')->insert([
                        'service_id' => $svc->id,
                        'title' => 'Default',
                        'body' => $body,
                        'version' => 'v1',
                        'effective_from' => null,
                        'effective_to' => null,
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('terms_and_conditions');
    }
};

