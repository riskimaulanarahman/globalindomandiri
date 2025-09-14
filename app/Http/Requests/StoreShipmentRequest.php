<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'resi_no' => ['nullable','string','unique:shipments,resi_no'],
            'customer_id' => ['required','exists:customers,id'],
            'sender_name' => ['nullable','string'],
            'sender_address' => ['nullable','string'],
            'receiver_name' => ['nullable','string'],
            'receiver_address' => ['nullable','string'],
            'origin_id' => ['required','exists:locations,id'],
            'destination_id' => ['required','exists:locations,id','different:origin_id'],
            'service_type' => ['required','in:Express,Regular,Udara,Laut,CharterPickup,CharterCDD,CharterLongbed,CharterTronton,Free'],
            'shipment_kind' => ['nullable','string'],
            'payment_method' => ['required','in:Cash,COD,Transfer,Invoice'],
            'items' => ['array'],
            'items.*.koli_no' => ['nullable','integer','min:1'],
            'items.*.weight_actual' => ['nullable','numeric','min:0'],
            'items.*.length_cm' => ['nullable','integer','min:0'],
            'items.*.width_cm' => ['nullable','integer','min:0'],
            'items.*.height_cm' => ['nullable','integer','min:0'],
        ];
    }
}

