<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'npwp' => $this->npwp,
            'payment_term_days' => $this->payment_term_days,
            'credit_limit' => $this->credit_limit,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}

