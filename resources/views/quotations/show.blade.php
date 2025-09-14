<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quotation {{ $quotation->quote_no }}</title>
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
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

    .sig { display:flex; justify-content: space-between; gap: 24px; margin-top: 32px; }
    .sig .box { flex: 1; text-align:center; }
    .sig .bar { height: 1px; background: var(--line); margin: 48px 0 6px; }

    @media print { .no-print { display:none; } }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="head flex between">
      <div class="brand">
        <img src="{{ public_path('img/rrgm-logo.png') }}" onerror="this.src='{{ asset('img/rrgm-logo.png') }}'" alt="Logo">
        <div>
          <div class="title">QUOTATION</div>
          <div class="org">{{ config('app.name') }}<br>{{ env('ORG_LINE1','') }}<br>{{ env('ORG_LINE2','') }}<br>{{ env('ORG_LINE3','') }}</div>
        </div>
      </div>
      <div class="text-right" style="font-size: 10px;">
        <div class="mb-1"><strong>No:</strong> <span class="mono">{{ $quotation->quote_no }}</span></div>
        <div class="mb-1"><strong>Date:</strong> {{ $quotation->quote_date?->format('Y-m-d') }}</div>
        <div class="mb-1"><strong>Valid Until:</strong> {{ $quotation->valid_until?->format('Y-m-d') }}</div>
        <div class="badge">{{ $quotation->status }}</div>
      </div>
    </div>

    <div class="flex between mb-6">
      <div style="width:58%">
        <div class="section-title">Dear,</div>
        <table class="kv">
          <tr><td><strong>Customer</strong></td><td>: {{ $quotation->customer?->name }}</td></tr>
          <tr><td><strong>PIC</strong></td><td>: {{ $quotation->attention ?: '—' }}</td></tr>
          <tr><td><strong>Phone</strong></td><td>: {{ $quotation->customer_phone ?: '—' }}</td></tr>
        </table>
      </div>
      <div style="width:40%">
        <div class="section-title">Currency</div>
        <table class="kv">
          <tr><td><strong>Currency</strong></td><td>: {{ $quotation->currency ?: 'IDR' }}</td></tr>
        </table>
      </div>
    </div>

    <table class="grid mb-4">
      <thead>
        <tr>
          <th style="width:36px">No</th>
          <th>Description</th>
          <th style="width:140px" class="text-right">Price</th>
          <th style="width:200px">Remarks</th>
          <th style="width:110px">Leadtime</th>
        </tr>
      </thead>
      <tbody>
        @php $sum=0; @endphp
        @forelse($quotation->lines as $line)
          @php $sum += (float)$line->amount; @endphp
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>
              @if($line->origin?->city)
                {{ $line->origin?->city }} → {{ $line->destination?->city }}
              @else
                {{ $line->description }}
              @endif
            </td>
            <td class="text-right">{{ $quotation->currency ?: 'IDR' }} {{ number_format((float)$line->unit_price,2) }}@if($line->uom) /{{ ucfirst(strtolower($line->uom)) }}@endif</td>
            <td>
              @if($line->remarks)
                {{ $line->remarks }}
              @else
                {{ trim(($line->service_type ? $line->service_type : '').($line->min_weight ? ' min. '.(int)$line->min_weight.'kg' : '')) ?: '—' }}
              @endif
            </td>
            <td>{{ $line->lead_time ?: '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center muted">No items</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="flex between mt-4" style="font-size: 10px;">
      <div style="width:58%">
        @if($quotation->paymentTerm)
          <div class="section-title">Payment Terms</div>
          <div class="mb-3">{{ $quotation->paymentTerm?->name }}</div>
        @endif

        @if(!$quotation->paymentTerm && $quotation->terms)
          <div class="section-title">Terms</div>
          <div class="mb-3">{{ $quotation->terms }}</div>
        @endif

        @if($quotation->terms_conditions)
          <div class="section-title">Terms & Conditions</div>
          <div class="mb-3" style="white-space:pre-line">{!! nl2br(e($quotation->terms_conditions)) !!}</div>
        @endif
      </div>
      {{-- <div style="width:40%">
        <div class="section-title">Summary</div>
        <table class="kv">
          <tr><td><strong>Total</strong></td><td class="text-right mono">{{ $quotation->currency ?: 'IDR' }} {{ number_format((float)$quotation->total,2) }}</td></tr>
          <tr><td><strong>Status</strong></td><td class="text-right">{{ $quotation->status }}</td></tr>
        </table>
      </div> --}}
    </div>

    {{-- <div class="muted text-center mt-6" style="font-size: 8px;">This document is generated by {{ config('app.name') }} • {{ now()->format('Y-m-d H:i') }}</div> --}}

    <div class="text-center mt-4 no-print">
      <button onclick="window.print()" style="padding:6px 10px;">Print</button>
    </div>
  </div>
</body>
</html>
