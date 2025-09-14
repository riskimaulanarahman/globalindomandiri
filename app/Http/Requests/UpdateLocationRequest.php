<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'city' => ['sometimes','required','string','max:255'],
            'province' => ['nullable','string','max:255'],
            'country' => ['nullable','string','max:10'],
        ];
    }
}

