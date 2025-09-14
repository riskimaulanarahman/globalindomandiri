<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DocumentSequence;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $status = trim((string)$request->get('status',''));
        $customerId = $request->integer('customer_id');
        $from = $request->get('from');
        $to = $request->get('to');

        $invoices = Invoice::with(['customer','payments'])
            ->when($q !== '', fn($qv) => $qv->where('invoice_no','like',"%$q%"))
            ->when($status !== '', fn($qv) => $qv->where('status',$status))
            ->when($customerId, fn($qv) => $qv->where('customer_id',$customerId))
            ->when($from, fn($qv) => $qv->whereDate('invoice_date','>=',$from))
            ->when($to, fn($qv) => $qv->whereDate('invoice_date','<=',$to))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get();
        $statuses = ['Draft','Sent','Partially Paid','Paid','Overdue'];

        return view('invoices.index', compact('invoices','customers','statuses','q','status','customerId','from','to'));
    }

    public function create(): View
    {
        $invoice = new Invoice(['status' => 'Draft', 'invoice_date' => now()->toDateString()]);
        $customers = Customer::orderBy('name')->get();
        return view('invoices.create', compact('invoice','customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'po_no' => ['nullable','string','max:100'],
            'invoice_date' => ['required','date'],
            'customer_id' => ['required','integer','exists:customers,id'],
            'due_date' => ['nullable','date','after_or_equal:invoice_date'],
            'top_days' => ['nullable','integer','min:0','max:365'],
            'terms_text' => ['nullable','string','max:500'],
            'remarks' => ['nullable','string','max:500'],
        ]);

        $data['invoice_no'] = $this->nextNumber();
        if (empty($data['due_date']) && !empty($data['top_days'])) {
            $data['due_date'] = now()->parse($data['invoice_date'])->addDays((int)$data['top_days'])->toDateString();
        }
        $data['status'] = 'Draft';
        $data['total_amount'] = 0;

        $invoice = Invoice::create($data);
        return redirect()->route('invoices.edit', $invoice)->with('status','created');
    }

    public function edit(Invoice $invoice): View
    {
        $invoice->load(['customer','lines.shipment','payments']);
        $customers = Customer::orderBy('name')->get();
        // eligible shipments: same customer and not already in invoice lines
        $eligibleShipments = Shipment::where('customer_id', $invoice->customer_id)
            ->whereNotIn('id', InvoiceLine::select('shipment_id'))
            ->orderByDesc('id')
            ->limit(50)
            ->get();
        $statuses = ['Draft','Sent','Partially Paid','Paid','Overdue'];
        return view('invoices.edit', compact('invoice','customers','eligibleShipments','statuses'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'po_no' => ['nullable','string','max:100'],
            'invoice_date' => ['required','date'],
            'customer_id' => ['required','integer','exists:customers,id'],
            'due_date' => ['nullable','date','after_or_equal:invoice_date'],
            'top_days' => ['nullable','integer','min:0','max:365'],
            'terms_text' => ['nullable','string','max:500'],
            'remarks' => ['nullable','string','max:500'],
            'status' => ['required','in:Draft,Sent,Partially Paid,Paid,Overdue'],
        ]);

        if (empty($data['due_date']) && !empty($data['top_days'])) {
            $data['due_date'] = now()->parse($data['invoice_date'])->addDays((int)$data['top_days'])->toDateString();
        }

        $invoice->update($data);
        $this->recalculate($invoice);
        return redirect()->route('invoices.edit', $invoice)->with('status','updated');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status !== 'Draft') {
            return back()->withErrors(['invoice' => "Hanya invoice berstatus 'Draft' yang bisa dihapus."]);
        }
        if ($invoice->payments()->exists()) {
            return back()->withErrors(['invoice' => 'Invoice has payments and cannot be deleted.']);
        }
        // Delete existing lines, then soft delete the invoice
        $invoice->lines()->delete();
        $invoice->delete();
        return redirect()->route('invoices.index')->with('status','deleted');
    }

    public function addLine(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'shipment_id' => ['nullable','integer','exists:shipments,id'],
            'description' => ['nullable','string','max:255'],
            'qty' => ['required','numeric','min:0.01'],
            'uom' => ['nullable','string','max:20'],
            'amount' => ['required','numeric','min:0'],
        ]);

        $desc = $data['description'] ?? null;
        if (!$desc && !empty($data['shipment_id'])) {
            $resi = Shipment::whereKey($data['shipment_id'])->value('resi_no');
            $desc = 'Shipment '.$resi;
        }

        // avoid duplicate shipment lines
        if (!empty($data['shipment_id']) && InvoiceLine::where('shipment_id',$data['shipment_id'])->exists()) {
            return back()->withErrors(['shipment_id' => 'This shipment is already invoiced.']);
        }

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'shipment_id' => $data['shipment_id'] ?? null,
            'description' => $desc,
            'qty' => $data['qty'],
            'uom' => $data['uom'] ?? 'Trip',
            'amount' => $data['amount'],
        ]);

        $this->recalculate($invoice);
        return back()->with('status','line-added');
    }

    public function removeLine(Invoice $invoice, InvoiceLine $line): RedirectResponse
    {
        if ($line->invoice_id !== $invoice->id) {
            abort(404);
        }
        $line->delete();
        $this->recalculate($invoice);
        return back()->with('status','line-removed');
    }

    public function print(Invoice $invoice): View
    {
        $invoice->load(['customer','lines.shipment','payments']);
        return view('invoices.print', compact('invoice'));
    }

    public function refreshFromShipments(Invoice $invoice): RedirectResponse
    {
        $invoice->load(['lines.shipment.origin','lines.shipment.destination']);
        foreach ($invoice->lines as $line) {
            if ($line->shipment) {
                $s = $line->shipment;
                $line->qty = 1;
                $line->uom = $line->uom ?: 'Trip';
                $line->amount = (float)($s->total_cost ?? 0);
                // keep description if user set custom; otherwise refresh
                if (!$line->description || str_starts_with($line->description, 'Shipment ')) {
                    $line->description = sprintf('Shipment %s %s → %s (%s)', $s->resi_no, $s->origin->city ?? '-', $s->destination->city ?? '-', $s->service_type);
                }
                $line->save();
            }
        }
        $this->recalculate($invoice);
        return back()->with('status','refreshed-from-shipments');
    }

    public function markSent(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'Draft') {
            $invoice->update(['status' => 'Sent']);
        }
        return back()->with('status','marked-sent');
    }

    private function nextNumber(): string
    {
        // Gunakan DocumentNumberService agar konsisten: INV/RGM/{MMYYYY}/00001
        $branch = env('APP_CODENAME', 'RGM');
        return app(\App\Services\DocumentNumberService::class)->nextInvoiceNo($branch);
    }

    private function recalculate(Invoice $invoice): void
    {
        $total = (float) $invoice->lines()->get()->reduce(function($carry,$line){
            return $carry + ((float)$line->qty * (float)$line->amount);
        }, 0.0);
        $invoice->update(['total_amount' => $total]);
    }
}
