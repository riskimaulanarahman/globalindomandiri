<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Shipment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function generateFromShipments(array $shipmentIds, int $customerId, string $branch, ?int $topDays = null): Invoice
    {
        return DB::transaction(function () use ($shipmentIds, $customerId, $branch, $topDays) {
            $shipmentIds = array_values(array_unique(array_map('intval', $shipmentIds)));
            if (empty($shipmentIds)) {
                abort(422, 'No shipments provided');
            }

            // Cek apakah shipment sudah pernah di-invoice
            $existingMap = InvoiceLine::whereIn('shipment_id', $shipmentIds)
                ->pluck('invoice_id', 'shipment_id'); // shipment_id => invoice_id

            // Idempotensi: ketika semua shipment sudah ada di invoice
            if (count($shipmentIds) === $existingMap->count()) {
                // Jika hanya satu shipment atau semua pada invoice yang sama, kembalikan invoice itu
                $uniqueInvoices = $existingMap->values()->unique();
                if ($uniqueInvoices->count() >= 1) {
                    return Invoice::findOrFail($uniqueInvoices->first());
                }
            }

            // Ambil shipment yang valid (customer cocok) dan belum di-invoice
            $remainingIds = array_values(array_diff($shipmentIds, $existingMap->keys()->all()));
            $shipments = Shipment::whereIn('id', $remainingIds)
                ->where('customer_id', $customerId)
                ->get();

            if ($shipments->isEmpty()) {
                // Fallback idempotensi untuk single shipment
                if (!empty($existingMap)) {
                    return Invoice::findOrFail($existingMap->values()->first());
                }
                abort(422, 'No eligible shipments to invoice');
            }

            $doc = app(DocumentNumberService::class)->nextInvoiceNo($branch);

            $invoice = new Invoice([
                'invoice_no' => $doc,
                'invoice_date' => now(),
                'customer_id' => $customerId,
                'top_days' => $topDays,
                'due_date' => $topDays ? now()->copy()->addDays($topDays) : null,
                'status' => 'Draft',
            ]);
            $invoice->save();

            $total = 0.0;
            foreach ($shipments as $s) {
                $desc = sprintf('Shipment %s %s → %s (%s)', $s->resi_no, $s->origin->city ?? '-', $s->destination->city ?? '-', $s->service_type);
                $line = new InvoiceLine([
                    'invoice_id' => $invoice->id,
                    'shipment_id' => $s->id,
                    'description' => $desc,
                    'qty' => 1,
                    'uom' => 'Trip',
                    'amount' => (float) ($s->total_cost ?? 0),
                ]);
                $line->save();
                $total += (float) $line->amount;
            }

            // Taxes already applied at shipment-level; invoice total is sum of lines.
            $invoice->total_amount = round($total, 2);
            $invoice->save();
            return $invoice;
        });
    }

    public function markSent(Invoice $invoice): Invoice
    {
        if ($invoice->status === 'Draft') {
            $invoice->status = 'Sent';
            $invoice->save();
        }
        return $invoice;
    }

    public function refreshStatus(Invoice $invoice): Invoice
    {
        $paid = (float) $invoice->payments()->sum('paid_amount');
        $outstanding = max(0.0, (float) ($invoice->total_amount ?? 0) - $paid);
        if ($outstanding <= 0 && $invoice->total_amount !== null) {
            $invoice->status = 'Paid';
        } elseif ($paid > 0) {
            $invoice->status = 'PartiallyPaid';
        } elseif ($invoice->due_date && now()->gt($invoice->due_date) && $outstanding > 0) {
            $invoice->status = 'Overdue';
        }
        $invoice->save();
        return $invoice;
    }
}
