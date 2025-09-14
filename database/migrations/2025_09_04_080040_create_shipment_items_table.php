<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->unsignedInteger('koli_no');
            $table->decimal('weight_actual', 10, 2)->nullable();
            $table->unsignedInteger('length_cm')->nullable();
            $table->unsignedInteger('width_cm')->nullable();
            $table->unsignedInteger('height_cm')->nullable();
            $table->decimal('volume_weight', 10, 2)->nullable();
            $table->decimal('billed_weight', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['shipment_id','koli_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};

