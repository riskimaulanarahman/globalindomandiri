<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., shipment, invoice
            $table->string('branch');
            $table->string('period'); // e.g., YYMM or YYYY
            $table->unsignedBigInteger('last_seq')->default(0);
            $table->timestamps();
            $table->unique(['type','branch','period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};

