<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('sender_pic', 100)->nullable()->after('sender_address');
            $table->string('sender_phone', 50)->nullable()->after('sender_pic');
            $table->string('receiver_pic', 100)->nullable()->after('receiver_address');
            $table->string('receiver_phone', 50)->nullable()->after('receiver_pic');
            $table->string('item_desc', 255)->nullable()->after('service_type');
            $table->string('notes', 255)->nullable()->after('item_desc');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['sender_pic','sender_phone','receiver_pic','receiver_phone','item_desc','notes']);
        });
    }
};

