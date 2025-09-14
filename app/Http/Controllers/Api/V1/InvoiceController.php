<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = Invoice::with(['customer','lines','payments']);
        if ($request->filled('status')) $q->where('status', $request->get('status'));
        if ($request->filled('customer_id')) $q->where('customer_id', $request->get('customer_id'));
        return InvoiceResource::collection($q->paginate(20));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = Invoice::create($request->validated());
        return new InvoiceResource($invoice);
    }

    public function show(Invoice $invoice)
    {
        return new InvoiceResource($invoice->load(['lines','payments','customer']));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $invoice->update($request->validated());
        return new InvoiceResource($invoice);
    }

    public function generate(GenerateInvoiceRequest $request, InvoiceService $svc)
    {
        $invoice = $svc->generateFromShipments(
            $request->validated('shipment_ids'),
            $request->validated('customer_id'),
            $request->validated('branch'),
            $request->validated('top_days')
        );
        return new InvoiceResource($invoice->load(['lines','payments','customer']));
    }

    public function send(Invoice $invoice)
    {
        app(InvoiceService::class)->markSent($invoice);
        // TODO: dispatch email job with PDF attachment
        return new InvoiceResource($invoice);
    }
}

