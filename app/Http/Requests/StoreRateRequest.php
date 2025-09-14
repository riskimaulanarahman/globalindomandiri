<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'origin_id' => ['required','exists:locations,id'],
            'destination_id' => ['required','exists:locations,id','different:origin_id'],
            'service_type' => ['required','in:Express,Regular,Udara,Laut,CharterPickup,CharterCDD,CharterLongbed,CharterTronton,Free'],
            'price' => ['required','numeric','min:0'],
            'lead_time' => ['required','string'],
            'min_weight' => ['nullable','integer','min:0'],
            'max_weight' => ['nullable','integer','min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
