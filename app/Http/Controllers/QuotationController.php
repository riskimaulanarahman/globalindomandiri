<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DocumentSequence;
use App\Models\Location;
use App\Models\Quotation;
use App\Models\PaymentTerm;
use App\Models\Service;
use App\Models\QuotationLine;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\TermsAndCondition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $status = trim((string)$request->get('status',''));
        $customerId = $request->integer('customer_id');
        $from = $request->get('from');
        $to = $request->get('to');

        $quotations = Quotation::with(['customer','origin','destination'])
            ->when($q !== '', fn($qq) => $qq->where('quote_no','like',"%$q%"))
            ->when($status !== '', fn($qq) => $qq->where('status',$status))
            ->when($customerId, fn($qq) => $qq->where('customer_id',$customerId))
            ->when($from, fn($qq) => $qq->whereDate('quote_date','>=',$from))
            ->when($to, fn($qq) => $qq->whereDate('quote_date','<=',$to))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get();
        $statuses = ['Draft','Sent','Accepted','Rejected','Expired','Converted','Closed'];
        return view('quotations.index', compact('quotations','customers','statuses','q','status','customerId','from','to'));
    }

    public function create(): View
    {
        $quotation = new Quotation([
            'status' => 'Draft',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'currency' => 'IDR',
        ]);
        $customers = Customer::orderBy('name')->get();
        $locations = Location::orderBy('city')->get();
        $paymentTerms = PaymentTerm::where('is_active', 1)->orderBy('name')->get();
        $services = Service::where('is_active', 1)->orderBy('name')->get();
        $tncList = TermsAndCondition::where('is_active', 1)->orderBy('title')->limit(200)->get();
        $statuses = ['Draft','Sent','Accepted','Rejected','Expired','Converted','Closed'];
        return view('quotations.create', compact('quotation','customers','locations','statuses','paymentTerms','services','tncList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateHeader($request);
        // Derive service_type from selected service (optional)
        if (!empty($data['service_id'])) {
            $svc = Service::find($data['service_id']);
            if ($svc) {
                $data['service_type'] = $svc->name;
            }
        }
        // Snapshot from selected T&C only (no auto-fallback)
        if (!empty($data['terms_and_conditions_id'])) {
            $tnc = TermsAndCondition::find($data['terms_and_conditions_id']);
            if ($tnc) {
                $data['terms_conditions'] = $tnc->body; // snapshot
            }
        }
        // Coerce nullable numerics to default values to avoid NULL inserts
        $data['discount_amt'] = isset($data['discount_amt']) ? (float)$data['discount_amt'] : 0.0;
        $data['tax_pct'] = isset($data['tax_pct']) ? (float)$data['tax_pct'] : 0.0;
        $data['quote_no'] = $this->nextQuoteNumber();
        $data['status'] = 'Draft';
        $data['subtotal'] = 0; $data['total'] = 0;
        $quotation = Quotation::create($data);
        return redirect()->route('quotations.edit', $quotation)->with('status','created');
    }

    public function edit(Quotation $quotation): View
    {
        $quotation->load(['customer','origin','destination','lines','termsDefinition']);
        $customers = Customer::orderBy('name')->get();
        $locations = Location::orderBy('city')->get();
        $paymentTerms = PaymentTerm::where('is_active', 1)->orderBy('name')->get();
        $services = Service::where('is_active', 1)->orderBy('name')->get();
        $tncList = TermsAndCondition::where('is_active', 1)
            ->orderBy('title')
            ->limit(200)
            ->get();
        $statuses = ['Draft','Sent','Accepted','Rejected','Expired','Converted','Closed'];
        // Suggest rates matching origin/destination if set
        $rates = Rate::with(['origin','destination'])
            ->when($quotation->origin_id, fn($q) => $q->where('origin_id', $quotation->origin_id))
            ->when($quotation->destination_id, fn($q) => $q->where('destination_id', $quotation->destination_id))
            ->orderByDesc('id')->limit(200)->get();
        $lines = QuotationLine::with(['origin','destination'])
            ->where('quotation_id', $quotation->id)
            ->orderBy('id')
            ->get();
        $relatedShipments = Shipment::with(['customer','origin','destination'])
            ->where('quotation_id', $quotation->id)
            ->orderByDesc('id')
            ->get();
        return view('quotations.edit', compact('quotation','customers','locations','statuses','rates','paymentTerms','services','tncList','lines','relatedShipments'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        if ($this->isLocked($quotation)) {
            return back()->withErrors(['quotation' => 'Quotation dengan status Rejected/Expired/Converted bersifat read-only dan tidak dapat diubah.']);
        }
        $data = $this->validateHeader($request, $quotation->id);
        // Derive service_type from selected service (optional)
        if (!empty($data['service_id'])) {
            $svc = Service::find($data['service_id']);
            if ($svc) {
                $data['service_type'] = $svc->name;
            }
        }
        // Snapshot from selected T&C only (no auto-fallback)
        if (!empty($data['terms_and_conditions_id'])) {
            $tnc = TermsAndCondition::find($data['terms_and_conditions_id']);
            if ($tnc) {
                $data['terms_conditions'] = $tnc->body; // snapshot
            }
        }
        // Default numeric fields when left blank
        if (!array_key_exists('discount_amt', $data) || $data['discount_amt'] === null || $data['discount_amt'] === '') {
            $data['discount_amt'] = 0.0;
        }
        if (!array_key_exists('tax_pct', $data) || $data['tax_pct'] === null || $data['tax_pct'] === '') {
            $data['tax_pct'] = 0.0;
        }
        $quotation->update($data);
        $this->recalc($quotation);
        $this->warnIfTncMismatch($quotation);
        return redirect()->route('quotations.edit',$quotation)->with('status','updated');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status === 'Converted') {
            return back()->withErrors(['quotation' => 'Converted quotation cannot be deleted.']);
        }
        $quotation->lines()->delete();
        $quotation->delete();
        return redirect()->route('quotations.index')->with('status','deleted');
    }

    public function addLine(Request $request, Quotation $quotation): RedirectResponse
    {
        if ($this->isLocked($quotation)) {
            return back()->withErrors(['quotation' => 'Tidak dapat menambah item pada quotation Rejected/Expired/Converted/Closed.']);
        }
        $data = $request->validate([
            'item_type' => ['nullable','in:route,custom'],
            'origin_id' => ['nullable','integer','exists:locations,id'],
            'destination_id' => ['nullable','integer','exists:locations,id','different:origin_id'],
            'service_type' => ['nullable','string','max:100'],
            'min_weight' => ['nullable','numeric','min:0'],
            'lead_time' => ['nullable','string','max:100'],
            'remarks' => ['nullable','string','max:255'],
            'description' => ['required_if:item_type,custom','nullable','string','max:255'],
            'qty' => ['nullable','numeric','min:0.01'],
            'uom' => ['nullable','string','max:20'],
            'unit_price' => ['required','numeric','min:0'],
        ]);
        // qty/uom: prefer provided, otherwise infer from service
        $itemType = $data['item_type'] ?? 'route';
        $qty = isset($data['qty']) ? (float)$data['qty'] : 1.0;
        if ($qty <= 0) $qty = 1.0;
        $svcName = strtolower(trim((string)($data['service_type'] ?? '')));
        // determine UOM: use provided; if route with empty UOM, infer from service; if custom with empty UOM, keep empty
        if (!empty($data['uom'])) {
            $uom = $data['uom'];
        } else {
            if ($itemType === 'route') {
                if (str_contains($svcName, 'charter')) {
                    $uom = 'Trip';
                } elseif (str_contains($svcName, 'express')) {
                    $uom = 'Colly';
                } else {
                    $uom = 'kg';
                }
            } else {
                // custom item: no UOM if not provided
                $uom = '';
            }
        }
        // auto-generate description jika tidak ada
        if (empty($data['description'])) {
            $parts = [];
            if (!empty($data['origin_id'])) {
                $o = \App\Models\Location::find($data['origin_id']);
                if ($o) $parts[] = $o->city;
            }
            if (!empty($data['destination_id'])) {
                $d = \App\Models\Location::find($data['destination_id']);
                if ($d) $parts[] = $d->city;
            }
            if (count($parts) === 2) {
                $desc = $parts[0] . ' → ' . $parts[1];
            } else {
                $desc = 'Item';
            }
            $svc = trim((string)($data['service_type'] ?? ''));
            if ($svc !== '') $desc .= ' | '.$svc;
            $mw = (float)($data['min_weight'] ?? 0);
            if ($mw > 0) $desc .= ' | min. '.((int)$mw).'kg';
            if (!empty($data['lead_time'])) $desc .= ' | LT '.$data['lead_time'];
            $data['description'] = $desc;
        }
        $amount = (float)$qty * (float)$data['unit_price'];
        QuotationLine::create([
            'quotation_id' => $quotation->id,
            'item_type' => $itemType,
            'origin_id' => $data['origin_id'] ?? null,
            'destination_id' => $data['destination_id'] ?? null,
            'service_type' => $data['service_type'] ?? null,
            'min_weight' => $data['min_weight'] ?? null,
            'lead_time' => $data['lead_time'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'description' => $data['description'],
            'qty' => $qty,
            'uom' => $uom,
            'unit_price' => $data['unit_price'],
            'amount' => $amount,
        ]);
        $this->recalc($quotation);
        $this->warnIfTncMismatch($quotation);
        return back()->with('status','line-added');
    }

    public function removeLine(Quotation $quotation, QuotationLine $line): RedirectResponse
    {
        if ($line->quotation_id !== $quotation->id) abort(404);
        if ($this->isLocked($quotation)) {
            return back()->withErrors(['quotation' => 'Tidak dapat menghapus item pada quotation Rejected/Expired/Converted/Closed.']);
        }
        $line->delete();
        $this->recalc($quotation);
        $this->warnIfTncMismatch($quotation);
        return back()->with('status','line-removed');
    }

    public function updateLine(Request $request, Quotation $quotation, QuotationLine $line): RedirectResponse
    {
        if ($line->quotation_id !== $quotation->id) abort(404);
        if ($this->isLocked($quotation)) {
            return back()->withErrors(['quotation' => 'Tidak dapat mengubah item pada quotation Rejected/Expired/Converted/Closed.']);
        }
        $data = $request->validate([
            'remarks' => ['nullable','string','max:255'],
        ]);
        $line->update([
            'remarks' => $data['remarks'] ?? null,
        ]);
        return back()->with('status','line-updated');
    }

    public function createShipmentFromLine(Quotation $quotation, QuotationLine $line): RedirectResponse
    {
        if ($line->quotation_id !== $quotation->id) abort(404);
        if ($quotation->status !== 'Accepted') {
            return back()->withErrors(['quotation' => 'Hanya quotation berstatus Accepted yang dapat dipakai.']);
        }
        if ($quotation->valid_until && now()->gt($quotation->valid_until)) {
            return back()->withErrors(['quotation' => 'Quotation sudah expired dan tidak dapat dipakai membuat shipment.']);
        }
        $originId = $line->origin_id ?: $quotation->origin_id;
        $destinationId = $line->destination_id ?: $quotation->destination_id;
        if (!$originId || !$destinationId) {
            return back()->withErrors(['quotation' => 'Item belum memiliki route (origin/destination).']);
        }
        $serviceType = $line->service_type ?: $quotation->service_type;
        $shipment = Shipment::create([
            'resi_no' => null,
            'customer_id' => $quotation->customer_id,
            'origin_id' => $originId,
            'destination_id' => $destinationId,
            'service_type' => $serviceType,
            'status' => 'Draft',
            'quote_no' => $quotation->quote_no,
            'quotation_id' => $quotation->id,
            'sender_name' => null,
            'sender_address' => null,
            'receiver_name' => null,
            'receiver_address' => null,
        ]);
        return redirect()->route('shipments.edit', $shipment)->with('status','created-from-quotation-line');
    }

    public function markSent(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status === 'Draft') $quotation->update(['status' => 'Sent']);
        return back()->with('status','marked-sent');
    }

    public function accept(Quotation $quotation): RedirectResponse
    {
        if (in_array($quotation->status, ['Draft','Sent'])) $quotation->update(['status' => 'Accepted']);
        return back()->with('status','accepted');
    }

    public function reject(Quotation $quotation): RedirectResponse
    {
        if (in_array($quotation->status, ['Draft','Sent'])) $quotation->update(['status' => 'Rejected']);
        return back()->with('status','rejected');
    }

    public function convert(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status !== 'Accepted') {
            return back()->withErrors(['quotation' => 'Only Accepted quotation can be used to create shipments.']);
        }
        // valid_until gating
        if ($quotation->valid_until && now()->gt($quotation->valid_until)) {
            return back()->withErrors(['quotation' => 'Quotation is expired and cannot be used to create a shipment.']);
        }
        $shipment = Shipment::create([
            'resi_no' => null,
            'customer_id' => $quotation->customer_id,
            'origin_id' => $quotation->origin_id,
            'destination_id' => $quotation->destination_id,
            'service_type' => $quotation->service_type,
            'status' => 'Draft',
            'quote_no' => $quotation->quote_no,
            'quotation_id' => $quotation->id,
            'sender_name' => null,
            'sender_address' => null,
            'receiver_name' => null,
            'receiver_address' => null,
        ]);
        // Keep quotation status as Accepted for reusability
        return redirect()->route('shipments.edit', $shipment)->with('status','created-from-quotation');
    }

    public function print(Quotation $quotation): View
    {
        $quotation->load(['customer','origin','destination','lines']);
        return view('quotations.show', compact('quotation'));
    }

    public function close(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status !== 'Accepted') {
            return back()->withErrors(['quotation' => 'Only Accepted quotation can be closed.']);
        }
        $quotation->update(['status' => 'Closed']);
        return back()->with('status','closed');
    }

    public function refreshTnc(Quotation $quotation, Request $request): RedirectResponse
    {
        if ($this->isLocked($quotation)) {
            return back()->withErrors(['quotation' => 'Tidak dapat re-apply T&C pada quotation locked.']);
        }
        $tncId = $request->integer('terms_and_conditions_id');
        if ($tncId) {
            $tnc = TermsAndCondition::find($tncId);
            if ($tnc) {
                $quotation->update([
                    'terms_and_conditions_id' => $tnc->id,
                    'terms_conditions' => $tnc->body,
                ]);
            }
        } elseif ($quotation->terms_and_conditions_id) {
            $tnc = TermsAndCondition::find($quotation->terms_and_conditions_id);
            if ($tnc) {
                $quotation->update(['terms_conditions' => $tnc->body]);
            }
        }
        $this->warnIfTncMismatch($quotation);
        return back()->with('status','tnc-reapplied');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['customer','origin','destination','lines']);
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quotations.show', compact('quotation'));
            return $pdf->download('quotation-'.$quotation->quote_no.'.pdf');
        }
        return redirect()->route('quotations.print', $quotation)->with('status','use-browser-print');
    }

    private function validateHeader(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'quote_date' => ['required','date'],
            'valid_until' => ['nullable','date','after_or_equal:quote_date'],
            'customer_id' => ['required','integer','exists:customers,id'],
            'origin_id' => ['nullable','integer','exists:locations,id'],
            'destination_id' => ['nullable','integer','exists:locations,id'],
            'service_type' => ['nullable','string','max:100'],
            'service_id' => ['nullable','integer','exists:services,id'],
            'lead_time' => ['nullable','string','max:100'],
            'currency' => ['nullable','string','max:10'],
            'tax_pct' => ['nullable','numeric','min:0'],
            'discount_amt' => ['nullable','numeric','min:0'],
            'attention' => ['nullable','string','max:100'],
            'customer_phone' => ['nullable','string','max:50'],
            'payment_term_id' => ['nullable','integer','exists:payment_terms,id'],
            'terms_and_conditions_id' => ['nullable','integer','exists:terms_and_conditions,id'],
            'terms_conditions' => ['nullable','string'],
        ]);
    }

    private function recalc(Quotation $quotation): void
    {
        $subtotal = (float) $quotation->lines()->sum('amount');
        $discount = (float) ($quotation->discount_amt ?? 0);
        $base = max(0.0, $subtotal - $discount);
        $taxPct = (float) ($quotation->tax_pct ?? 0);
        $taxAmt = $base * ($taxPct / 100.0);
        $total = max(0.0, $base + $taxAmt);
        $quotation->update(['subtotal' => $subtotal, 'total' => $total]);
    }

    private function nextQuoteNumber(): string
    {
        $appcode = env('APP_CODENAME', 'RGM');
        $period = now()->format('Ym');
        $seq = DocumentSequence::firstOrCreate([
            'type' => 'QRGM',
            // 'branch' => (string)$appcode,
            'period' => $period,
        ], ['last_seq' => 0]);
        $seq->last_seq = (int)$seq->last_seq + 1;
        $seq->save();
        return sprintf('QRGM-%s-%04d', $period, $seq->last_seq);
    }

    private function isLocked(Quotation $quotation): bool
    {
        return in_array($quotation->status, ['Rejected','Expired','Converted','Closed']);
    }

    private function warnIfTncMismatch(Quotation $quotation): void
    {
        // If no selected T&C, nothing to validate
        if (!$quotation->terms_and_conditions_id) return;

        $svcNames = $quotation->lines()->whereNotNull('service_type')->pluck('service_type')->unique()->filter()->values();
        if ($svcNames->isEmpty()) return;

        $services = Service::whereIn('name', $svcNames)->get(['id','name']);
        $serviceIdsOnLines = $services->pluck('id')->all();

        $tnc = TermsAndCondition::with('services:id')
            ->find($quotation->terms_and_conditions_id);
        if (!$tnc) return;
        $covered = collect($tnc->services)->pluck('id')->all();

        $missing = [];
        foreach ($services as $svc) {
            if (!in_array($svc->id, $covered, true)) {
                $missing[] = $svc->name;
            }
        }
        if (!empty($missing)) {
            session()->flash('warning', 'T&C terpilih tidak mencakup service: '.implode(', ', $missing).'. Anda tetap dapat melanjutkan, namun pastikan T&C sesuai.');
        }
    }
}
