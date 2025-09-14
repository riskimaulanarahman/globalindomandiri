<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'shipment_ids' => ['required','array','min:1'],
            'shipment_ids.*' => ['integer','exists:shipments,id'],
            'customer_id' => ['required','exists:customers,id'],
            'branch' => ['required','string','max:10'],
            'top_days' => ['nullable','integer','min:0'],
        ];
    }
}

