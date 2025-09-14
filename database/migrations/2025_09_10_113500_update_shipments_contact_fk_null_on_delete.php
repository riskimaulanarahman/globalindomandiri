<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'sender_contact_id')) {
                try { $table->dropForeign(['sender_contact_id']); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('shipments', 'receiver_contact_id')) {
                try { $table->dropForeign(['receiver_contact_id']); } catch (\Throwable $e) {}
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'sender_contact_id')) {
                $table->foreign('sender_contact_id')
                    ->references('id')
                    ->on('customer_contacts')
                    ->nullOnDelete();
            }
            if (Schema::hasColumn('shipments', 'receiver_contact_id')) {
                $table->foreign('receiver_contact_id')
                    ->references('id')
                    ->on('customer_contacts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'sender_contact_id')) {
                try { $table->dropForeign(['sender_contact_id']); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('shipments', 'receiver_contact_id')) {
                try { $table->dropForeign(['receiver_contact_id']); } catch (\Throwable $e) {}
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'sender_contact_id')) {
                $table->foreign('sender_contact_id')
                    ->references('id')
                    ->on('customer_contacts');
            }
            if (Schema::hasColumn('shipments', 'receiver_contact_id')) {
                $table->foreign('receiver_contact_id')
                    ->references('id')
                    ->on('customer_contacts');
            }
        });
    }
};

