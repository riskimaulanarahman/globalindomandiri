<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_lines', 'item_type')) {
                $table->string('item_type', 20)->nullable()->after('quotation_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotation_lines', function (Blueprint $table) {
            if (Schema::hasColumn('quotation_lines', 'item_type')) {
                $table->dropColumn('item_type');
            }
        });
    }
};

