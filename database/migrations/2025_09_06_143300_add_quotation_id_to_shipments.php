<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'quotation_id')) {
                $table->foreignId('quotation_id')->nullable()->after('quote_no')->constrained('quotations');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'quotation_id')) {
                $table->dropConstrainedForeignId('quotation_id');
            }
        });
    }
};
