<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destination_id')->constrained('locations');
            $table->enum('service_type', ['Express','Regular','Udara','Laut','CharterPickup','CharterCDD','CharterLongbed','CharterTronton','Free']);
            $table->decimal('price', 12, 2);
            $table->string('lead_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['origin_id','destination_id','service_type'], 'rates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};

