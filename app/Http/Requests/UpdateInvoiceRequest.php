<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('invoice')?->id;
        return [
            'invoice_no' => ['nullable','string','unique:invoices,invoice_no,'.($id ?? 'NULL')],
            'invoice_date' => ['nullable','date'],
            'customer_id' => ['sometimes','required','exists:customers,id'],
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

