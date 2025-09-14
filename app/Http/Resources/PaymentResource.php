<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'paid_amount' => $this->paid_amount,
            'paid_date' => $this->paid_date,
            'method' => $this->method,
            'ref_no' => $this->ref_no,
        ];
    }
}

