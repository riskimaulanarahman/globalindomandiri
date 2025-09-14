<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'payment_term_id')) {
                $table->unsignedBigInteger('payment_term_id')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('quotations', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->after('service_type');
            }
            if (!Schema::hasColumn('quotations', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('payment_term_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'terms_conditions')) {
                $table->dropColumn('terms_conditions');
            }
            if (Schema::hasColumn('quotations', 'service_id')) {
                $table->dropColumn('service_id');
            }
            if (Schema::hasColumn('quotations', 'payment_term_id')) {
                $table->dropColumn('payment_term_id');
            }
        });
    }
};
