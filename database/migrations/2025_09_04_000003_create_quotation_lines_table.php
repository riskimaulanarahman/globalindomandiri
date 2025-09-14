<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('rate_id')->nullable();
            $table->string('description', 255);
            $table->decimal('qty', 12, 2)->default(1);
            $table->string('uom', 20)->default('kg');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->index(['quotation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_lines');
    }
};

