<?php

namespace App\Services;

use App\Models\Rate;
use App\Models\Shipment;

class SlaService
{
    // Parse lead_time like "1-2 DAYS" => max days = 2
    public function parseMaxDays(string $leadTime): ?int
    {
        if (preg_match('/(\d+)(?:\s*-\s*(\d+))?/i', $leadTime, $m)) {
            return isset($m[2]) ? (int) $m[2] : (int) $m[1];
        }
        return null;
    }

    public function evaluate(Shipment $shipment, ?Rate $rate = null): bool
    {
        $rate = $rate ?: $shipment->rate;
        if (!$rate || !$shipment->departed_at || !$shipment->received_at) {
            return false;
        }
        $maxDays = $this->parseMaxDays($rate->lead_time);
        if ($maxDays === null) {
            return false;
        }
        $onTime = $shipment->departed_at->diffInDays($shipment->received_at) <= $maxDays;
        $shipment->sla_on_time = $onTime;
        $shipment->save();
        return $onTime;
    }
}

