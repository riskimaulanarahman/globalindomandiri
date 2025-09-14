<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'invoice_id' => ['required','exists:invoices,id'],
            'paid_amount' => ['required','numeric','min:0.01'],
            'paid_date' => ['nullable','date'],
            'method' => ['nullable','string','max:100'],
            'ref_no' => ['nullable','string','max:100'],
        ];
    }
}

