<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('invoice_date')->nullable();
            $table->foreignId('customer_id')->constrained('customers');
            $table->unsignedInteger('top_days')->nullable();
            $table->text('terms_text')->nullable();
            $table->date('received_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->enum('status', ['Draft','Sent','PartiallyPaid','Paid','Overdue','Cancelled'])->default('Draft');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['status','due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

