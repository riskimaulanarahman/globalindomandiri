<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'city' => ['required','string','max:255'],
            'province' => ['nullable','string','max:255'],
            'country' => ['nullable','string','max:10'],
        ];
    }
}

