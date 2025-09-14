<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_lines', 'origin_id')) {
                $table->foreignId('origin_id')->nullable()->after('rate_id')->constrained('locations');
            }
            if (!Schema::hasColumn('quotation_lines', 'destination_id')) {
                $table->foreignId('destination_id')->nullable()->after('origin_id')->constrained('locations');
            }
            if (!Schema::hasColumn('quotation_lines', 'service_type')) {
                $table->string('service_type', 100)->nullable()->after('destination_id');
            }
            if (!Schema::hasColumn('quotation_lines', 'min_weight')) {
                $table->decimal('min_weight', 10, 2)->nullable()->after('service_type');
            }
            if (!Schema::hasColumn('quotation_lines', 'lead_time')) {
                $table->string('lead_time', 100)->nullable()->after('min_weight');
            }
            if (!Schema::hasColumn('quotation_lines', 'remarks')) {
                $table->string('remarks', 255)->nullable()->after('lead_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotation_lines', function (Blueprint $table) {
            if (Schema::hasColumn('quotation_lines', 'remarks')) $table->dropColumn('remarks');
            if (Schema::hasColumn('quotation_lines', 'lead_time')) $table->dropColumn('lead_time');
            if (Schema::hasColumn('quotation_lines', 'min_weight')) $table->dropColumn('min_weight');
            if (Schema::hasColumn('quotation_lines', 'service_type')) $table->dropColumn('service_type');
            if (Schema::hasColumn('quotation_lines', 'destination_id')) $table->dropConstrainedForeignId('destination_id');
            if (Schema::hasColumn('quotation_lines', 'origin_id')) $table->dropConstrainedForeignId('origin_id');
        });
    }
};
