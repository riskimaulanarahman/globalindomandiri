<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('payment_terms')->insert([
            ['name' => 'Cash Before Delivery', 'code' => 'CBD', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cash After Delivery',  'code' => 'CAD', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Others',                'code' => 'OTH', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
    }
};

