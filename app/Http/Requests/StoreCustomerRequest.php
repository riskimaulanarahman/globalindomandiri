<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'code' => ['nullable','string','max:100','unique:customers,code'],
            'name' => ['required','string','max:255'],
            'pic' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:100'],
            'email' => ['nullable','email'],
            'npwp' => ['nullable','string','max:100'],
            'payment_term_days' => ['nullable','integer','min:0'],
            'credit_limit' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string'],
        ];
    }
}

