<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DocumentSequence;
use App\Models\InvoiceLine;
use App\Models\Location;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\Service;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $status = trim((string)$request->get('status',''));
        $customerId = $request->integer('customer_id');
        $fromDate = $request->get('from');
        $toDate = $request->get('to');

        $shipments = Shipment::with(['customer','origin','destination','invoiceLine.invoice'])
            ->when($q !== '', fn($qr) => $qr->where('resi_no','like',"%$q%"))
            ->when($status !== '', fn($qr) => $qr->where('status', $status))
            ->when($customerId, fn($qr) => $qr->where('customer_id', $customerId))
            ->when($fromDate, fn($qr) => $qr->whereDate('departed_at','>=',$fromDate))
            ->when($toDate, fn($qr) => $qr->whereDate('departed_at','<=',$toDate))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get();
        $statuses = ['Draft','Booked','In Transit','Delivered','Cancelled'];

        return view('shipments.index', compact('shipments','customers','statuses','q','status','customerId','fromDate','toDate'));
    }

    public function create(): View
    {
        $shipment = new Shipment(['status' => 'Draft']);
        $customers = Customer::orderBy('name')->get();
        $locations = Location::orderBy('city')->get();
        $rates = Rate::with(['origin','destination'])->orderByDesc('id')->limit(200)->get();
        $statuses = ['Draft','Booked','In Transit','Delivered','Cancelled'];
        $services = Service::where('is_active', 1)->orderBy('name')->get();
        return view('shipments.create', compact('shipment','customers','locations','rates','statuses','services'));
    }

    public function store(Request $request): RedirectResponse
    {
        // allow auto-generate resi on create when not provided
        $validated = $this->validateShipment($request, null, false);

        if (empty($validated['resi_no'])) {
            $validated['resi_no'] = $this->nextResiNumber();
        }

        $this->applyDerivedFields($validated);

        $shipment = Shipment::create($validated);
        return redirect()->route('shipments.edit', $shipment)->with('status','created');
    }

    public function edit(Shipment $shipment): View
    {
        $shipment->load(['invoiceLine.invoice']);
        $customers = Customer::orderBy('name')->get();
        $locations = Location::orderBy('city')->get();
        $rates = Rate::with(['origin','destination'])->orderByDesc('id')->limit(200)->get();
        $statuses = ['Draft','Booked','In Transit','Delivered','Cancelled'];
        $services = Service::where('is_active', 1)->orderBy('name')->get();
        return view('shipments.edit', compact('shipment','customers','locations','rates','statuses','services'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        // allow auto-generate resi on update when not provided (e.g., converted from quotation)
        $validated = $this->validateShipment($request, $shipment->id, false);
        if (empty($validated['resi_no'])) {
            $validated['resi_no'] = $shipment->resi_no ?: $this->nextResiNumber();
        }
        $this->applyDerivedFields($validated);
        $shipment->update($validated);
        return redirect()->route('shipments.edit', $shipment)->with('status','updated');
    }

    public function destroy(Shipment $shipment): RedirectResponse
    {
        // prevent delete if already invoiced
        if (InvoiceLine::where('shipment_id', $shipment->id)->exists()) {
            return back()->withErrors(['shipment' => 'Shipment already invoiced and cannot be deleted.']);
        }
        $shipment->delete();
        return redirect()->route('shipments.index')->with('status','deleted');
    }

    public function createInvoice(Request $request, Shipment $shipment, InvoiceService $invoiceService): RedirectResponse
    {
        // Jika shipment sudah diinvoice, redirect ke invoice terkait
        $existing = InvoiceLine::where('shipment_id', $shipment->id)->first();
        if ($existing) {
            return redirect()->route('invoices.edit', $existing->invoice_id)->with('status','already-invoiced');
        }

        $branch = env('APP_CODENAME', 'RGM');
        $topDays = $shipment->customer?->payment_term_days ?? null;
        $invoice = $invoiceService->generateFromShipments([$shipment->id], (int)$shipment->customer_id, $branch, $topDays);
        return redirect()->route('invoices.edit', $invoice)->with('status','created-from-shipment');
    }

    private function validateShipment(Request $request, ?int $id = null, bool $requireResi = true): array
    {
        return $request->validate([
            'resi_no' => [$requireResi ? 'required' : 'nullable','string','max:100'],
            'customer_id' => ['required','integer','exists:customers,id'],
            'origin_id' => ['required','integer','exists:locations,id'],
            'destination_id' => ['required','integer','exists:locations,id','different:origin_id'],
            'sender_customer_id' => ['nullable','integer','exists:customers,id'],
            'receiver_customer_id' => ['nullable','integer','exists:customers,id'],
            'sender_contact_id' => ['nullable','integer', Rule::exists('customer_contacts','id')->where(function($q) use ($request) {
                $cust = $request->input('sender_customer_id');
                if ($cust) $q->where('customer_id', $cust);
            })],
            'receiver_contact_id' => ['nullable','integer', Rule::exists('customer_contacts','id')->where(function($q) use ($request) {
                $cust = $request->input('receiver_customer_id');
                if ($cust) $q->where('customer_id', $cust);
            })],
            'service_type' => ['required','string','max:100'],
            'item_desc' => ['nullable','string','max:255'],
            'notes' => ['nullable','string','max:255'],
            'weight_actual' => ['nullable','numeric','min:0'],
            'volume_weight' => ['nullable','numeric','min:0'],
            'koli_count' => ['nullable','integer','min:0'],
            'rate_id' => ['nullable','integer','exists:rates,id'],
            'base_fare' => ['nullable','numeric','min:0'],
            'packing_fee' => ['nullable','numeric','min:0'],
            'insurance_fee' => ['nullable','numeric','min:0'],
            'discount' => ['nullable','numeric','min:0'],
            'ppn' => ['nullable','numeric','min:0'],
            'pph23' => ['nullable','numeric','min:0'],
            'other_fee' => ['nullable','numeric','min:0'],
            'departed_at' => ['nullable','date'],
            'received_at' => ['nullable','date','after_or_equal:departed_at'],
            'status' => ['required','in:Draft,Booked,In Transit,Delivered,Cancelled'],
            'sender_name' => ['nullable','string','max:255'],
            'sender_address' => ['nullable','string','max:500'],
            'sender_pic' => ['nullable','string','max:100'],
            'sender_phone' => ['nullable','string','max:50'],
            'receiver_name' => ['nullable','string','max:255'],
            'receiver_address' => ['nullable','string','max:500'],
            'receiver_pic' => ['nullable','string','max:100'],
            'receiver_phone' => ['nullable','string','max:50'],
        ]);
    }

    private function applyDerivedFields(array &$data): void
    {
        $weightActual = (float)($data['weight_actual'] ?? 0);
        $volumeWeight = (float)($data['volume_weight'] ?? 0);
        $data['weight_charge'] = max($weightActual, $volumeWeight);

        // Calculate base fare from selected rate if available
        if (!empty($data['rate_id']) && empty($data['base_fare'])) {
            $rate = Rate::find($data['rate_id']);
            if ($rate) {
                $data['base_fare'] = (float)$rate->price * (float)($data['weight_charge'] ?? 0);
            }
        }

        $base = (float)($data['base_fare'] ?? 0);
        $packing = (float)($data['packing_fee'] ?? 0);
        $ins = (float)($data['insurance_fee'] ?? 0);
        $other = (float)($data['other_fee'] ?? 0);
        $disc = (float)($data['discount'] ?? 0);
        $ppn = (float)($data['ppn'] ?? 0);
        $pph = (float)($data['pph23'] ?? 0);
        $subtotal = max(0.0, $base + $packing + $ins + $other - $disc);
        $data['total_cost'] = max(0.0, $subtotal + $ppn - $pph);
    }

    private function nextResiNumber(): string
    {
        $period = now()->format('Ym');
        $seq = DocumentSequence::firstOrCreate([
            'type' => 'RESI',
            'branch' => null,
            'period' => $period,
        ], ['last_seq' => 0]);
        $seq->last_seq = (int)$seq->last_seq + 1;
        $seq->save();
        return sprintf('RGM-%s-%05d', $period, $seq->last_seq);
    }

    public function awb(Shipment $shipment): \Illuminate\View\View
    {
        $shipment->load(['customer','origin','destination']);
        return view('shipments.awb', compact('shipment'));
    }

    public function awbBarcode(Shipment $shipment): \Illuminate\View\View
    {
        $shipment->load(['customer','origin','destination']);
        return view('shipments.awb_barcode', compact('shipment'));
    }

}
