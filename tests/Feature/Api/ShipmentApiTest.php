<?php

use App\Models\Customer;
use App\Models\Location;

it('stores shipment and computes weights', function () {
    $customer = Customer::factory()->create();
    $origin = Location::factory()->create();
    $dest = Location::factory()->create();
    $payload = [
        'customer_id' => $customer->id,
        'origin_id' => $origin->id,
        'destination_id' => $dest->id,
        'service_type' => 'Regular',
        'payment_method' => 'Cash',
        'items' => [
            ['koli_no'=>1,'weight_actual'=>10,'length_cm'=>50,'width_cm'=>40,'height_cm'=>30],
        ],
    ];
    $resp = $this->postJson('/api/v1/shipments', $payload);
    $resp->assertStatus(200);
    $data = $resp->json('data');
    expect(is_numeric($data['weights']['billed']))->toBeTrue();
    expect((float)$data['weights']['billed'])->toBeGreaterThan(0);
});
