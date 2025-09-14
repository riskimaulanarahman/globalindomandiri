<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add address to customer_contacts
        if (Schema::hasTable('customer_contacts')) {
            Schema::table('customer_contacts', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_contacts', 'address')) {
                    $table->text('address')->nullable()->after('email');
                }
            });
        }

        // Backfill: move basic contact info from customers into contacts (as default)
        if (Schema::hasTable('customers')) {
            $hasPic = Schema::hasColumn('customers', 'pic');
            $hasPhone = Schema::hasColumn('customers', 'phone');
            $hasEmail = Schema::hasColumn('customers', 'email');
            $hasAddr = Schema::hasColumn('customers', 'address');
            if ($hasPic || $hasPhone || $hasEmail || $hasAddr) {
                DB::table('customers')->orderBy('id')->chunk(100, function($rows) use ($hasPic,$hasPhone,$hasEmail,$hasAddr) {
                    foreach ($rows as $row) {
                        $name = $hasPic ? ($row->pic ?? null) : null;
                        $phone = $hasPhone ? ($row->phone ?? null) : null;
                        $email = $hasEmail ? ($row->email ?? null) : null;
                        $addr = $hasAddr ? ($row->address ?? null) : null;
                        if (($name && trim($name) !== '') || ($phone && trim($phone) !== '') || ($email && trim($email) !== '') || ($addr && trim($addr) !== '')) {
                            DB::table('customer_contacts')->insert([
                                'customer_id' => $row->id,
                                'name' => $name && trim($name) !== '' ? $name : 'Main PIC',
                                'phone' => $phone,
                                'email' => $email,
                                'address' => $addr,
                                'is_default' => 1,
                                'notes' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                });
            }
        }

        // Drop columns from customers
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers','pic')) $table->dropColumn('pic');
            if (Schema::hasColumn('customers','phone')) $table->dropColumn('phone');
            if (Schema::hasColumn('customers','email')) $table->dropColumn('email');
            if (Schema::hasColumn('customers','address')) $table->dropColumn('address');
        });
    }

    public function down(): void
    {
        // Add columns back to customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers','pic')) $table->string('pic')->nullable()->after('name');
            if (!Schema::hasColumn('customers','phone')) $table->string('phone')->nullable()->after('pic');
            if (!Schema::hasColumn('customers','email')) $table->string('email')->nullable()->after('phone');
            if (!Schema::hasColumn('customers','address')) $table->text('address')->nullable()->after('email');
        });

        // Optionally backfill from default contact
        if (Schema::hasTable('customer_contacts')) {
            DB::table('customer_contacts')->where('is_default',1)->orderBy('id')->chunk(100, function($rows){
                foreach ($rows as $cc) {
                    DB::table('customers')->where('id',$cc->customer_id)->update([
                        'pic' => $cc->name,
                        'phone' => $cc->phone,
                        'email' => $cc->email,
                        'address' => $cc->address,
                    ]);
                }
            });
        }

        // Drop address from contacts
        if (Schema::hasTable('customer_contacts')) {
            Schema::table('customer_contacts', function (Blueprint $table) {
                if (Schema::hasColumn('customer_contacts','address')) $table->dropColumn('address');
            });
        }
    }
};
