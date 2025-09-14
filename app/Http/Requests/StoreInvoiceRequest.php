<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'invoice_no' => ['nullable','string','unique:invoices,invoice_no'],
            'invoice_date' => ['nullable','date'],
            'customer_id' => ['required','exists:customers,id'],
            'top_days' => ['nullable','integer','min:0'],
            'terms_text' => ['nullable','string'],
            'received_date' => ['nullable','date'],
            'due_date' => ['nullable','date'],
            'total_amount' => ['nullable','numeric','min:0'],
            'status' => ['nullable','in:Draft,Sent,PartiallyPaid,Paid,Overdue,Cancelled'],
            'remarks' => ['nullable','string'],
        ];
    }
}

