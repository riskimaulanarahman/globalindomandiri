<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function recordPayment(Invoice $invoice, float $amount, ?string $method = null, ?string $ref = null, ?string $date = null): Payment
    {
        return DB::transaction(function () use ($invoice, $amount, $method, $ref, $date) {
            $payment = new Payment([
                'invoice_id' => $invoice->id,
                'paid_amount' => round($amount, 2),
                'paid_date' => $date ? date('Y-m-d', strtotime($date)) : now()->toDateString(),
                'method' => $method,
                'ref_no' => $ref,
            ]);
            $payment->save();
            app(InvoiceService::class)->refreshStatus($invoice->fresh(['payments']));
            return $payment;
        });
    }
}

