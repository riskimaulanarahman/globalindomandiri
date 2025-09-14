<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('resi_no')->unique();
            $table->string('letter_no')->nullable();
            $table->string('quote_no')->nullable();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('sender_name')->nullable();
            $table->text('sender_address')->nullable();
            $table->string('receiver_name')->nullable();
            $table->text('receiver_address')->nullable();
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destination_id')->constrained('locations');
            $table->enum('service_type', ['Express','Regular','Udara','Laut','CharterPickup','CharterCDD','CharterLongbed','CharterTronton','Free']);
            $table->string('shipment_kind')->nullable();
            $table->enum('payment_method', ['Cash','COD','Transfer','Invoice']);
            $table->decimal('weight_charge', 10, 2)->nullable();
            $table->decimal('weight_actual', 10, 2)->nullable();
            $table->decimal('volume_weight', 10, 2)->nullable();
            $table->unsignedInteger('koli_count')->nullable();
            $table->decimal('base_fare', 12, 2)->nullable();
            $table->decimal('packing_fee', 12, 2)->nullable();
            $table->decimal('insurance_fee', 12, 2)->nullable();
            $table->decimal('discount', 12, 2)->nullable();
            $table->decimal('ppn', 12, 2)->nullable();
            $table->decimal('pph23', 12, 2)->nullable();
            $table->decimal('other_fee', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->dateTime('departed_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->enum('status', ['Draft','ReceivedAtOrigin','InTransit','ReceivedAtDestination','Delivered','Cancelled'])->default('Draft');
            $table->foreignId('rate_id')->nullable()->constrained('rates');
            $table->string('sales_owner')->nullable();
            $table->boolean('sla_on_time')->default(false);
            $table->timestamps();
            $table->index(['status','departed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

