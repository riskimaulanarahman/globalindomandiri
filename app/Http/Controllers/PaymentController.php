<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $method = trim((string)$request->get('method',''));
        $from = $request->get('from');
        $to = $request->get('to');

        $payments = Payment::with('invoice')
            ->when($q !== '', function ($qr) use ($q) {
                $qr->whereHas('invoice', function ($iq) use ($q) {
                    $iq->where('invoice_no', 'like', "%$q%");
                })->orWhere('ref_no','like',"%$q%");
            })
            ->when($method !== '', fn($qr) => $qr->where('method',$method))
            ->when($from, fn($qr) => $qr->whereDate('paid_date','>=',$from))
            ->when($to, fn($qr) => $qr->whereDate('paid_date','<=',$to))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $methods = ['Transfer','Cash','Credit Card','Other'];
        return view('payments.index', compact('payments','methods','q','method','from','to'));
    }

    public function create(Request $request): View
    {
        $payment = new Payment([
            'paid_date' => now()->toDateString(),
            'method' => 'Transfer',
            'invoice_id' => $request->integer('invoice_id') ?: null,
        ]);
        $invoices = Invoice::orderByDesc('id')->limit(100)->get();
        $methods = ['Transfer','Cash','Credit Card','Other'];
        return view('payments.create', compact('payment','invoices','methods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'invoice_id' => ['required','integer','exists:invoices,id'],
            'paid_amount' => ['required','numeric','min:0.01'],
            'paid_date' => ['required','date'],
            'method' => ['required','string','max:50'],
            'ref_no' => ['nullable','string','max:100'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);
        if ((float)$data['paid_amount'] > (float)$invoice->outstanding) {
            return back()->withInput()->withErrors(['paid_amount' => 'Amount exceeds invoice outstanding.']);
        }

        Payment::create($data);
        $this->adjustInvoiceStatus($invoice->fresh());
        return redirect()->route('payments.index')->with('status','created');
    }

    public function edit(Payment $payment): View
    {
        $invoices = Invoice::orderByDesc('id')->limit(100)->get();
        $methods = ['Transfer','Cash','Credit Card','Other'];
        return view('payments.edit', compact('payment','invoices','methods'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'invoice_id' => ['required','integer','exists:invoices,id'],
            'paid_amount' => ['required','numeric','min:0.01'],
            'paid_date' => ['required','date'],
            'method' => ['required','string','max:50'],
            'ref_no' => ['nullable','string','max:100'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);

        // compute hypothetical outstanding after this update
        $currentInvoice = $payment->invoice; // before change
        $payment->fill($data);

        // If moving payment to a different invoice, adjust both later via adjustInvoiceStatus
        $payment->save();

        // Validate not exceeding after save
        $invoice = $invoice->fresh();
        if ((float)$invoice->outstanding < 0) {
            // revert amount and show error
            $payment->refresh();
            $payment->update(['paid_amount' => max(0.0, (float)$payment->paid_amount + (float)$invoice->outstanding)]);
            return back()->withErrors(['paid_amount' => 'Amount exceeds invoice outstanding.'])->withInput();
        }

        // adjust statuses
        if ($currentInvoice && $currentInvoice->id !== $invoice->id) {
            $this->adjustInvoiceStatus($currentInvoice->fresh());
        }
        $this->adjustInvoiceStatus($invoice->fresh());

        return redirect()->route('payments.index')->with('status','updated');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $invoice = $payment->invoice;
        $payment->delete();
        if ($invoice) {
            $this->adjustInvoiceStatus($invoice->fresh());
        }
        return redirect()->route('payments.index')->with('status','deleted');
    }

    private function adjustInvoiceStatus(Invoice $invoice): void
    {
        $invoice->refresh();
        $paid = (float)$invoice->paid_amount;
        $total = (float)($invoice->total_amount ?? 0);
        $out = max(0.0, $total - $paid);
        $newStatus = $invoice->status;

        if ($total <= 0) {
            $newStatus = 'Draft';
        } elseif ($out <= 0.00001) {
            $newStatus = 'Paid';
        } elseif ($paid > 0) {
            // Overdue check takes precedence if due date passed
            if ($invoice->due_date && now()->gt($invoice->due_date)) {
                $newStatus = 'Overdue';
            } else {
                $newStatus = 'Partially Paid';
            }
        } else {
            // no payment yet
            if ($invoice->due_date && now()->gt($invoice->due_date)) {
                $newStatus = 'Overdue';
            } elseif ($invoice->status === 'Draft') {
                $newStatus = 'Sent'; // consider sent once managed here
            }
        }

        if ($newStatus !== $invoice->status) {
            $invoice->update(['status' => $newStatus]);
        }
    }
}
