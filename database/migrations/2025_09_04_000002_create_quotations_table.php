<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_no', 40)->unique();
            $table->date('quote_date');
            $table->date('valid_until')->nullable();
            $table->string('status', 20)->default('Draft');

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('origin_id')->nullable();
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->string('service_type', 100)->nullable();
            $table->string('lead_time', 100)->nullable();

            $table->string('currency', 10)->default('IDR');
            $table->decimal('tax_pct', 5, 2)->nullable();
            $table->decimal('discount_amt', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->string('attention', 100)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('terms', 500)->nullable();
            $table->string('notes', 500)->nullable();

            $table->string('branch', 20)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};

