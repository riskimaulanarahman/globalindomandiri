<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'invoice_date' => $this->invoice_date,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'top_days' => $this->top_days,
            'due_date' => $this->due_date,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'lines' => InvoiceLineResource::collection($this->whenLoaded('lines')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}

