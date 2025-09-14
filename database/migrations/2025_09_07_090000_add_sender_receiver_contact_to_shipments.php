<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'sender_contact_id')) {
                $table->foreignId('sender_contact_id')->nullable()->after('sender_customer_id')->constrained('customer_contacts');
            }
            if (!Schema::hasColumn('shipments', 'receiver_contact_id')) {
                $table->foreignId('receiver_contact_id')->nullable()->after('receiver_customer_id')->constrained('customer_contacts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'receiver_contact_id')) {
                $table->dropConstrainedForeignId('receiver_contact_id');
            }
            if (Schema::hasColumn('shipments', 'sender_contact_id')) {
                $table->dropConstrainedForeignId('sender_contact_id');
            }
        });
    }
};

