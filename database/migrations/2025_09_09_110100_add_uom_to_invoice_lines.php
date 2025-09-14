<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_lines', 'uom')) {
                $table->string('uom', 20)->nullable()->after('qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_lines', 'uom')) {
                $table->dropColumn('uom');
            }
        });
    }
};

