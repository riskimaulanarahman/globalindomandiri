<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice {{ $invoice->invoice_no }}</title>
  <style>
    @page { size: A4 portrait; margin: 14mm; }
    :root {
      --ink: #1f2937;       /* slate-800 */
      --muted: #6b7280;     /* gray-500 */
      --line: #e5e7eb;      /* gray-200 */
      --shade: #f9fafb;     /* gray-50 */
      --accent: #111827;    /* gray-900 */
    }
    * { box-sizing: border-box; }
    body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif; color: var(--ink); }
    .wrap { max-width: 780px; margin: 0 auto; }
    .flex { display:flex; }
    .between { justify-content: space-between; align-items: center; }
    .mt-2 { margin-top: 8px; } .mt-4 { margin-top: 16px; } .mt-6 { margin-top: 24px; }
    .mb-1 { margin-bottom: 4px; } .mb-2 { margin-bottom: 8px; } .mb-3 { margin-bottom: 12px; } .mb-4 { margin-bottom: 16px; } .mb-6 { margin-bottom: 24px; }
    .text-right { text-align: right; } .text-center { text-align:center; }
    .muted { color: var(--muted); }
    .title { font-size: 26px; font-weight: 800; letter-spacing: .5px; }
    .badge { display:inline-block; background: var(--shade); border:1px solid var(--line); padding:2px 8px; border-radius: 999px; font-size: 12px; }

    .head { border-bottom: 2px solid var(--accent); padding-bottom: 8px; margin-bottom: 12px; }
    .brand { display:flex; align-items:center; }
    .brand img { height: 120px; width:auto; margin-right: 12px; }
    .org { font-size: 12px; line-height: 1.3; }

    .grid { width:100%; border:1px solid var(--line); border-collapse: collapse; }
    .grid th, .grid td { border:1px solid var(--line); padding:8px 10px; vertical-align: top; font-size: 12px; }
    .grid thead th { background: var(--shade); font-weight: 600; }
    .grid tfoot th { background: #fff; }
    .no-border { border:none !important; }

    .kv { width:100%; border-collapse:collapse; }
    .kv td { padding: 4px 0; font-size: 12px; }
    .hr { height:1px; background: var(--line); border:0; margin: 8px 0 12px; }

    .section-title { font-weight: 700; font-size: 13px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .3px; }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; white-space: nowrap; }
    .inv-no { font-size: 16px; font-weight: 700; letter-spacing: .3px; }
    .sig-box { position: relative; height: 120px; border:1px dashed var(--line); }
    .stamp { position:absolute; top:50%; left:42%; transform: translate(-50%, -50%) rotate(-8deg); width: 120px; opacity: .6; z-index: 1; }
    .sign { position:absolute; top:62%; left:62%; transform: translate(-50%, -50%); width: 160px; opacity: .95; z-index: 2; }

    @media print { .no-print { display:none; } }
  </style>
  </head>
<body>
  <div class="wrap">
    @php
      // Simple IDR currency formatter used in this print view only
      $idr = function ($value, $withPrefix = true) {
        $num = number_format((float)($value ?? 0), 0, ',', '.');
        return $withPrefix ? ('Rp '.$num) : $num;
      };
      // Terbilang (Indonesian number words) for integers up to trillions
      $terbilang = function ($val) {
        $val = (int) round((float)($val ?? 0));
        if ($val === 0) return 'Nol';
        $units = ['', 'Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        $spell = function($n) use (&$spell, $units) {
          $n = (int)$n;
          if ($n < 12) return $units[$n];
          if ($n < 20) return trim($units[$n-10].' Belas');
          if ($n < 100) return trim($spell(intval($n/10)).' Puluh '.($n%10? $spell($n%10):''));
          if ($n < 200) return trim('Seratus '.($n-100? $spell($n-100):''));
          if ($n < 1000) return trim($spell(intval($n/100)).' Ratus '.($n%100? $spell($n%100):''));
          if ($n < 2000) return trim('Seribu '.($n-1000? $spell($n-1000):''));
          if ($n < 1000000) return trim($spell(intval($n/1000)).' Ribu '.($n%1000? $spell($n%1000):''));
          if ($n < 1000000000) return trim($spell(intval($n/1000000)).' Juta '.($n%1000000? $spell($n%1000000):''));
          if ($n < 1000000000000) return trim($spell(intval($n/1000000000)).' Miliar '.($n%1000000000? $spell($n%1000000000):''));
          if ($n < 1000000000000000) return trim($spell(intval($n/1000000000000)).' Triliun '.($n%1000000000000? $spell($n%1000000000000):''));
          return (string)$n; // fallback for extremely large numbers
        };
        return $spell(abs($val));
      };
    @endphp
    <div class="head flex between">
      <div class="brand">
        <img src="{{ public_path('img/rrgm-logo.png') }}" onerror="this.src='{{ asset('img/rrgm-logo.png') }}'" alt="Logo">
        <div>
          <div class="title">INVOICE</div>
          <div class="org">{{ config('app.name') }}<br>{{ env('ORG_LINE1','') }}<br>{{ env('ORG_LINE2','') }}<br>{{ env('ORG_LINE3','') }}</div>
        </div>
      </div>
      <div class="text-right" style="font-size: 10px;">
        <div class="badge">{{ $invoice->status }}</div>
      </div>
    </div>

    <div class="text-center inv-no mb-2">No: <span class="mono">{{ $invoice->invoice_no }}</span></div>

    <div class="flex between mb-6">
      <div style="width:58%">
        <div class="section-title">Bill To</div>
        <table class="kv">
          <tr><td><strong>Customer</strong></td><td>: {{ $invoice->customer?->name }}</td></tr>
        </table>
      </div>
      <div style="width:40%">
        <table class="kv">
          <tr><td><strong>PO No</strong></td><td class="text-right mono">{{ $invoice->po_no ?: '—' }}</td></tr>
          <tr><td><strong>Tanggal Terbit</strong></td><td class="text-right mono">{{ $invoice->invoice_date?->format('Y-m-d') ?: '—' }}</td></tr>
          <tr><td><strong>Jatuh Tempo</strong></td><td class="text-right mono">{{ $invoice->due_date?->format('Y-m-d') ?: '—' }}</td></tr>
          <tr><td><strong>Term Of Payment</strong></td><td class="text-right mono">{{ $invoice->terms_text ? $invoice->terms_text : 'Cash' }}</td></tr>
        </table>
      </div>
    </div>

    <table class="grid mb-4">
      <thead>
        <tr>
          <th style="width:36px">No</th>
          <th>Description</th>
          <th style="width:70px" class="text-right">Koli</th>
          <th style="width:90px" class="text-right">Weight (kg)</th>
          <th style="width:110px" class="text-right">Price</th>
          <th style="width:120px" class="text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        @php $sum=0; @endphp
        @forelse($invoice->lines as $line)
          @php
            $s = $line->shipment;
            $koli = (int)($s?->koli_count ?? (int)($line->qty ?? 0));
            $weight = (float)($s?->weight_charge ?? 0);
            $ratePrice = $s?->rate?->price;
            $price = (float)($ratePrice ?? ($weight > 0 ? ((float)($s?->base_fare ?? 0)) / $weight : (float)($line->amount ?? 0)));
            $lt = (float)$price * (float)$weight;
            $sum += $lt;
          @endphp
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>
              {{ $line->description }}
              @if($line->shipment?->resi_no)
                <div class="muted" style="font-size:10px;">Resi: {{ $line->shipment?->resi_no }}</div>
              @endif
            </td>
            <td class="text-right mono">{{ number_format((float)$koli,0,',','.') }}</td>
            <td class="text-right mono">{{ number_format((float)$weight,2,',','.') }}</td>
            <td class="text-right mono">{{ $idr($price) }}</td>
            <td class="text-right mono">{{ $idr($lt) }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center muted">No items</td></tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr>
          <th colspan="5" class="text-right">Grand Total</th>
          <th class="text-right mono">{{ $idr($invoice->total_amount) }}</th>
        </tr>
        <tr>
          <th colspan="5" class="text-right">Paid</th>
          <th class="text-right mono">{{ $idr($invoice->paid_amount) }}</th>
        </tr>
        <tr>
          <th colspan="5" class="text-right">Outstanding</th>
          <th class="text-right mono">{{ $idr($invoice->outstanding) }}</th>
        </tr>
      </tfoot>
    </table>

    @php
      $dpp = 0.0; $ppn = 0.0; $pph = 0.0;
      foreach ($invoice->lines as $l) {
        $s = $l->shipment;
        if ($s) {
          $base = (float)($s->base_fare ?? 0);
          $pack = (float)($s->packing_fee ?? 0);
          $ins  = (float)($s->insurance_fee ?? 0);
          $oth  = (float)($s->other_fee ?? 0);
          $disc = (float)($s->discount ?? 0);
          $dpp += max(0.0, $base + $pack + $ins + $oth - $disc);
          $ppn += (float)($s->ppn ?? 0);
          $pph += (float)($s->pph23 ?? 0);
        }
      }
      $balance = max(0.0, $dpp - $pph + $ppn);
    @endphp

    <div class="flex between mt-4" style="font-size: 10px;">
      <div style="width:58%">
        @if($invoice->remarks)
          <div class="section-title">Remarks</div>
          <div class="mb-3">{{ $invoice->remarks }}</div>
        @endif
        
        @php
          $grand = (float)($invoice->outstanding ?? 0);
          if ($grand <= 0) { $grand = (float)($invoice->total_amount ?? 0); }
        @endphp
        <div class="section-title">Terbilang</div>
        <div class="mb-3">{{ strtoupper($terbilang($grand)) }} RUPIAH</div>
        <div class="section-title">Payment to Account</div>
        <div class="mb-3" style="border:1px solid var(--line); padding:8px 10px;">
          <div><strong>{{ env('BANK_NAME','') ?: '—' }}</strong></div>
          <div>{{ env('BANK_ACC_NO','') ?: '—' }}</div>
          <div>{{ env('BANK_ACC_NAME','') ?: '' }}</div>
        </div>
      </div>
      <div style="width:40%">
        <div class="section-title">Tax Summary</div>
        <table class="kv" style="width:100%;">
          <tr><td><strong>DPP</strong></td><td class="text-right mono">{{ $idr($dpp) }}</td></tr>
          <tr><td><strong>PPh23</strong></td><td class="text-right mono">{{ $idr($pph) }}</td></tr>
          <tr><td><strong>PPN</strong></td><td class="text-right mono">{{ $idr($ppn) }}</td></tr>
          <tr><td><strong>Balance</strong></td><td class="text-right mono">{{ $idr($balance) }}</td></tr>
        </table>

        <div class="mt-6">
          <div class="section-title">Tanda Tangan</div>
          <div class="muted mb-1">Disetujui,</div>
          <div class="sig-box">
            @php 
              $stampFile = public_path('img/stamp_rrgm.png');
              $signFile = public_path('img/sign_rrgm.png');
            @endphp
            @if(file_exists($stampFile))
              <img src="{{ asset('img/stamp_rrgm.png') }}" alt="Stamp" class="stamp">
            @endif
            @if(file_exists($signFile))
              <img src="{{ asset('img/sign_rrgm.png') }}" alt="Signature" class="sign">
            @endif
          </div>
          <div class="muted" style="font-size:10px;">TTD & Stempel</div>
        </div>
      </div>
    </div>

    <div class="text-center mt-4 no-print">
      <button onclick="window.print()" style="padding:6px 10px;">Print</button>
    </div>

    <!-- <div class="foot text-center mt-4">This document is generated by {{ config('app.name') }} • {{ now()->format('Y-m-d H:i') }}</div> -->
  </div>
</body>
</html>
