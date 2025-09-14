<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Location;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        foreach (['Admin','Finance','Ops','Sales','Viewer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Admin user
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('Admin');

        Location::factory()->count(10)->create();
        Customer::factory()->count(5)->create();

        // Create up to 30 unique rates
        $locations = Location::pluck('id')->all();
        $serviceTypes = ['Express','Regular','Udara','Laut'];
        $created = 0;
        shuffle($locations);
        foreach ($locations as $o) {
            foreach ($locations as $d) {
                if ($o === $d) continue;
                foreach ($serviceTypes as $st) {
                    if ($created >= 30) break 3;
                    Rate::firstOrCreate([
                        'origin_id' => $o,
                        'destination_id' => $d,
                        'service_type' => $st,
                    ], [
                        'price' => rand(5000,25000),
                        'lead_time' => '1-2 DAYS',
                        'is_active' => true,
                    ]);
                    $created++;
                }
            }
        }

        // Create 20 shipments with random items and basic pricing
        $shipments = Shipment::factory()->count(20)->create();
        $pricing = app(\App\Services\ShipmentPricingService::class);
        foreach ($shipments as $s) {
            $itemCount = rand(1, 4);
            for ($i=1; $i <= $itemCount; $i++) {
                $len = rand(20,80); $wid = rand(20,80); $hei = rand(20,80);
                $item = ShipmentItem::create([
                    'shipment_id' => $s->id,
                    'koli_no' => $i,
                    'weight_actual' => rand(1, 50),
                    'length_cm' => $len,
                    'width_cm' => $wid,
                    'height_cm' => $hei,
                ]);
            }
            $pricing->recomputeShipmentWeights($s->fresh('items'));
            $pricing->applyRateAndCharges($s->fresh());
        }

        // Create 5 invoices with some shipments each
        $customers = Customer::all();
        $chunks = $shipments->chunk(4)->take(5);
        foreach ($chunks as $chunk) {
            $customerId = $chunk->first()->customer_id;
            $invoice = app(\App\Services\InvoiceService::class)->generateFromShipments($chunk->pluck('id')->all(), $customerId, env('DOC_PREFIX_BRANCH','JKT'), 30);
            $invoice->status = 'Sent';
            $invoice->save();
        }
    }
}
