<?php

namespace App\Observers;

use App\Models\Shipment;
use App\Services\DocumentNumberService;

class ShipmentNumberObserver
{
    public function creating(Shipment $shipment): void
    {
        if (empty($shipment->resi_no)) {
            $branch = env('DOC_PREFIX_BRANCH', 'JKT');
            $shipment->resi_no = app(DocumentNumberService::class)->nextShipmentResi($branch);
        }
    }
}

