<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            if (!Schema::hasColumn('rates', 'max_weight')) {
                $table->unsignedInteger('max_weight')->nullable()->after('lead_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            if (Schema::hasColumn('rates', 'max_weight')) {
                $table->dropColumn('max_weight');
            }
        });
    }
};

