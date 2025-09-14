<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'resi_no' => $this->resi_no,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'origin' => new LocationResource($this->whenLoaded('origin')),
            'destination' => new LocationResource($this->whenLoaded('destination')),
            'service_type' => $this->service_type,
            'payment_method' => $this->payment_method,
            'weights' => [
                'actual' => $this->weight_actual,
                'volume' => $this->volume_weight,
                'billed' => $this->weight_charge,
            ],
            'koli_count' => $this->koli_count,
            'amounts' => [
                'base_fare' => $this->base_fare,
                'packing_fee' => $this->packing_fee,
                'insurance_fee' => $this->insurance_fee,
                'discount' => $this->discount,
                'ppn' => $this->ppn,
                'pph23' => $this->pph23,
                'other_fee' => $this->other_fee,
                'total_cost' => $this->total_cost,
            ],
            'status' => $this->status,
            'departed_at' => $this->departed_at,
            'received_at' => $this->received_at,
            'items' => ShipmentItemResource::collection($this->whenLoaded('items')),
        ];
    }
}

