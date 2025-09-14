<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'origin_id' => ['sometimes','required','exists:locations,id'],
            'destination_id' => ['sometimes','required','exists:locations,id','different:origin_id'],
            'service_type' => ['sometimes','required','in:Express,Regular,Udara,Laut,CharterPickup,CharterCDD,CharterLongbed,CharterTronton,Free'],
            'price' => ['sometimes','required','numeric','min:0'],
            'lead_time' => ['sometimes','required','string'],
            'min_weight' => ['sometimes','nullable','integer','min:0'],
            'max_weight' => ['sometimes','nullable','integer','min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
