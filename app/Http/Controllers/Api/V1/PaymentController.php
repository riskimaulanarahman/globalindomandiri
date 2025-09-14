<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request, PaymentService $svc)
    {
        $invoice = Invoice::where('id', $request->validated('invoice_id'))->firstOrFail();
        $payment = $svc->recordPayment(
            $invoice,
            (float) $request->validated('paid_amount'),
            $request->validated('method') ?? null,
            $request->validated('ref_no') ?? null,
            $request->validated('paid_date') ?? null,
        );
        return new PaymentResource($payment->load('invoice'));
    }
}

