<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'origin' => new LocationResource($this->whenLoaded('origin')),
            'destination' => new LocationResource($this->whenLoaded('destination')),
            'service_type' => $this->service_type,
            'price' => $this->price,
            'lead_time' => $this->lead_time,
            'min_weight' => $this->min_weight,
            'max_weight' => $this->max_weight,
            'is_active' => $this->is_active,
        ];
    }
}
