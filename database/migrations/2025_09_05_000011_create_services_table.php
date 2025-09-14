<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 50)->unique();
            $table->text('default_terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $tnc = "- subject to actual weight and dimension\n- rates exclude VAT\n- rates exclude packing\n- rates exclude cargo insurance\n- transaction payment to account :\n      - R&R Globalindo Mandiri\n         BCA : 12345678";
        $rows = [
            ['name' => 'Carter Pickup', 'code' => 'CARTER_PICKUP'],
            ['name' => 'Carter Colt Diesel Double', 'code' => 'CARTER_CDD'],
            ['name' => 'Colt Diesel Engkel', 'code' => 'CDE'],
            ['name' => 'Carter Long Bed', 'code' => 'CARTER_LONGBED'],
            ['name' => 'Carter Tronton', 'code' => 'CARTER_TRONTON'],
            ['name' => 'Udara', 'code' => 'AIR'],
            ['name' => 'Laut', 'code' => 'SEA'],
            ['name' => 'Regular', 'code' => 'REG'],
            ['name' => 'Express', 'code' => 'EXP'],
        ];
        foreach ($rows as $r) {
            DB::table('services')->insert([
                'name' => $r['name'],
                'code' => $r['code'],
                'default_terms' => $tnc,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
