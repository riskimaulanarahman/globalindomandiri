<?php

use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Services\ShipmentPricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates billed weight as max(volume, actual)', function () {
    $shipment = Shipment::factory()->create();
    ShipmentItem::create(['shipment_id'=>$shipment->id,'koli_no'=>1,'weight_actual'=>10,'length_cm'=>50,'width_cm'=>40,'height_cm'=>30]);
    ShipmentItem::create(['shipment_id'=>$shipment->id,'koli_no'=>2,'weight_actual'=>5,'length_cm'=>30,'width_cm'=>30,'height_cm'=>30]);
    $svc = new ShipmentPricingService();
    $weights = $svc->recomputeShipmentWeights($shipment->fresh('items'));
    expect($weights['billed'])->toBeFloat()->and($weights['billed'])->toBeGreaterThanOrEqual($weights['actual'])->and($weights['billed'])->toBeGreaterThanOrEqual($weights['volume']);
});
