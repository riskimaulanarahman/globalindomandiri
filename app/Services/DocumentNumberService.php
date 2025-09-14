<?php

namespace App\Services;

use App\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentNumberService
{
    public function nextShipmentResi(string $branch): string
    {
        $prefix = env('DOC_PREFIX_BRANCH', $branch);
        $period = now()->format('ym');
        $seq = $this->nextSequence('shipment', $branch, $period, 4);
        return sprintf('%s-%s-%s', $prefix, strtoupper($period), $seq);
    }

    public function nextInvoiceNo(string $branch): string
    {
        // Format yang diminta: INV/RGM/{MMYYYY}/00001
        $prefix = 'INV';
        $branch = strtoupper($branch ?: 'RGM');
        $period = now()->format('mY'); // MMYYYY
        $seq = $this->nextSequence('invoice', $branch, $period, 5);
        return sprintf('%s/%s/%s/%s', $prefix, $branch, $period, $seq);
    }

    public function nextCustomerCode(string $branch): string
    {
        // Customer code sequence is global (not per-period)
        $branch = strtoupper($branch ?: env('APP_CODENAME', 'RGM'));
        $period = 'ALL';
        $seq = $this->nextSequence('CUST', $branch, $period, 4);
        return sprintf('%s-CUST-%s', $branch, $seq);
    }

    protected function nextSequence(string $type, string $branch, string $period, int $pad): string
    {
        return DB::transaction(function () use ($type, $branch, $period, $pad) {
            $row = DocumentSequence::where(compact('type','branch','period'))
                ->lockForUpdate()->first();
            if (!$row) {
                $row = DocumentSequence::create([
                    'type' => $type,
                    'branch' => $branch,
                    'period' => $period,
                    'last_seq' => 0,
                ]);
            }
            $row->last_seq = $row->last_seq + 1;
            $row->save();
            return str_pad((string) $row->last_seq, $pad, '0', STR_PAD_LEFT);
        });
    }
}
