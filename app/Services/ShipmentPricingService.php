<?php

namespace App\Services;

use App\Models\Rate;
use App\Models\Shipment;
use App\Models\ShipmentItem;

class ShipmentPricingService
{
    public function computeItemVolumeWeight(ShipmentItem $item, int $divisor = null): float
    {
        $divisor = $divisor ?? (int) config('app.volume_divisor', (int) env('VOLUME_DIVISOR', 6000));
        if (!$item->length_cm || !$item->width_cm || !$item->height_cm || $divisor <= 0) {
            return (float) ($item->volume_weight ?? 0);
        }
        $vw = (($item->length_cm * $item->width_cm * $item->height_cm) / $divisor);
        return round($vw, 2);
    }

    public function recomputeShipmentWeights(Shipment $shipment): array
    {
        $items = $shipment->items;
        $totalActual = 0.0;
        $totalVolume = 0.0;
        foreach ($items as $item) {
            $vw = $this->computeItemVolumeWeight($item);
            if ($vw > 0 && $item->volume_weight != $vw) {
                $item->volume_weight = $vw;
                $item->save();
            }
            $totalActual += (float) ($item->weight_actual ?? 0);
            $totalVolume += (float) ($item->volume_weight ?? 0);
        }
        $billed = max($totalVolume, $totalActual);
        $shipment->weight_actual = round($totalActual, 2);
        $shipment->volume_weight = round($totalVolume, 2);
        $shipment->weight_charge = round($billed, 2);
        $shipment->koli_count = $items->count();
        $shipment->save();
        return ['actual' => $shipment->weight_actual, 'volume' => $shipment->volume_weight, 'billed' => $shipment->weight_charge];
    }

    public function applyRateAndCharges(Shipment $shipment, ?Rate $rate = null): Shipment
    {
        $rate = $rate ?: Rate::where('origin_id', $shipment->origin_id)
            ->where('destination_id', $shipment->destination_id)
            ->where('service_type', $shipment->service_type)
            ->first();

        $shipment->rate_id = $rate?->id;

        $base = 0.0;
        if ($rate) {
            // Charter can be flat; otherwise price per kg (simple default).
            $isCharter = str_starts_with($rate->service_type, 'Charter');
            $base = $isCharter ? (float) $rate->price : (float) $rate->price * (float) ($shipment->weight_charge ?? 0);
        }

        $packing = (float) ($shipment->packing_fee ?? 0);
        $insurance = (float) ($shipment->insurance_fee ?? 0);
        $discount = (float) ($shipment->discount ?? 0);
        $other = (float) ($shipment->other_fee ?? 0);

        $ppnRate = (float) env('PPN_RATE', 0.11);
        $pphRate = (float) env('PPH23_RATE', 0.0);

        $subTotal = max(0.0, $base + $packing + $insurance + $other - $discount);
        $ppn = round($subTotal * $ppnRate, 2);
        $pph = round($subTotal * $pphRate, 2);
        $total = round($subTotal + $ppn - $pph, 2);

        $shipment->base_fare = round($base, 2);
        $shipment->ppn = $ppn;
        $shipment->pph23 = $pph;
        $shipment->total_cost = $total;
        $shipment->save();

        return $shipment;
    }
}

