<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'sender_customer_id')) {
                $table->foreignId('sender_customer_id')->nullable()->after('customer_id')->constrained('customers');
            }
            if (!Schema::hasColumn('shipments', 'receiver_customer_id')) {
                $table->foreignId('receiver_customer_id')->nullable()->after('sender_customer_id')->constrained('customers');
            }
        });

        // Default existing data: set sender_customer_id = customer_id
        DB::statement('UPDATE shipments SET sender_customer_id = customer_id WHERE sender_customer_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'receiver_customer_id')) {
                $table->dropConstrainedForeignId('receiver_customer_id');
            }
            if (Schema::hasColumn('shipments', 'sender_customer_id')) {
                $table->dropConstrainedForeignId('sender_customer_id');
            }
        });
    }
};

