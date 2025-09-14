<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'koli_no' => $this->koli_no,
            'weight_actual' => $this->weight_actual,
            'length_cm' => $this->length_cm,
            'width_cm' => $this->width_cm,
            'height_cm' => $this->height_cm,
            'volume_weight' => $this->volume_weight,
            'billed_weight' => $this->billed_weight,
        ];
    }
}

