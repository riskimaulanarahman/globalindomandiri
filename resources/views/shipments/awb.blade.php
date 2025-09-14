<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resi {{ $shipment->resi_no }}</title>
  <style>
    @page { size: A4 landscape; margin: 14mm; }
    body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; color:#000; }
    .header { text-align:center; margin-bottom:6px; }
    .brand { display:inline-flex; align-items:center; justify-content:center;margin-right: 70px; }
    .brand img { height: 100px; margin-right: 0px; }
    .brand h1 { margin:0; font-size: 30px; letter-spacing: .5px; font-weight: 800; }
    .org-lines { font-size: 10px; line-height: 1.4; margin-top: -34px; margin-bottom: 20px }
    .right { text-align:right; }
    .meta { display:flex; align-items:center; justify-content:space-between; margin: 6px 0 10px; }
    .muted { color:#555; }
    .title { text-align:center; font-weight: 800; font-size: 18px; text-transform: uppercase; margin: 10px 0; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #000; padding:6px 8px; vertical-align: top; font-size: 12px; }
    th { background:#f5f5f5; }
    .no-border { border:none; }
    .note-red { color:#b10000; font-weight: 800; font-size: 18px }
    .grid { width:100%; border:1px solid #000; border-collapse: collapse; margin-top: 8px; }
    .grid td { border:1px solid #000; padding:6px 8px; }
    .sign { text-align:center; padding: 28px 6px; height: 70px; }
    .small { font-size: 11px; }
    .mb-6 { margin-bottom: 6px; }
    .mb-10 { margin-bottom: 10px; }
    .mb-14 { margin-bottom: 14px; }
    .nowrap { white-space: nowrap; }
    .text-right { text-align: right; }
    .w-60 { width: 60%; }
    .w-40 { width: 40%; }
    @media print {
      .no-print { display:none; }
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="brand">
      <img src="{{ public_path('img/rrgm-logo.png') }}" onerror="this.src='{{ asset('img/rrgm-logo.png') }}'" alt="Logo">
      <div>
        <h1><u>{{ strtoupper(config('app.name')) }}</u></h1>
      </div>
    </div>
    <div class="org-lines">
      <div>{{ env('ORG_LINE1','') }}</div>
      <div>{{ env('ORG_LINE2','') }}</div>
      <div>{{ env('ORG_LINE3','') }}</div>
    </div>
  </div>

  <div class="meta">
    <div class="left"><strong>{{ $shipment->origin?->city }}</strong>, {{ ($shipment->departed_at ?? now())->format('j M Y') }}</div>
    <div class="right"><span class="muted">Nomor Resi:</span> <strong class="nowrap">{{ $shipment->resi_no }}</strong></div>
  </div>

  <div class="title">Tanda Terima Titipan</div>

  <table class="mb-10">
    <tr>
      <td class="w-60"><strong>KEPADA</strong> : {{ $shipment->receiver_name }}</td>
      <td class="w-40" rowspan="2"><strong>ISI BARANG</strong><br>{{ $shipment->item_desc ?? '—' }}</td>
    </tr>
    <tr>
      <td><strong>ALAMAT</strong> : {{ $shipment->receiver_address }}</td>
    </tr>
    <tr>
      <td><strong>PIC</strong> : {{ $shipment->receiver_pic ?? '—' }}</td>
      <td rowspan="2"><strong><span class="nowrap">JENIS KIRIMAN</span></strong><br>{{ strtoupper($shipment->service_type) }}</td>
    </tr>
    <tr>
      <td><strong>TLP</strong> : {{ $shipment->receiver_phone ?? '—' }}</td>
    </tr>
    <tr>
      <td><strong>PENGIRIM</strong> : {{ $shipment->sender_name }}</td>
      <td rowspan="4"><strong>KETERANGAN</strong><br><br>
        <span class="note-red">{{ $shipment->notes ?? '—' }}</span>
      </td>
    </tr>
    <tr>
      <td><strong>ALAMAT</strong> : {{ $shipment->sender_address }}</td>
    </tr>
    <tr>
      <td><strong>PIC</strong> : {{ $shipment->sender_pic ?? '—' }}</td>
    </tr>
    <tr>
      <td><strong>TLP</strong> : {{ $shipment->sender_phone ?? '—' }}</td>
    </tr>
  </table>

  <table class="grid">
    <colgroup>
      <col style="width:30%">
      <col style="width:17.5%">
      <col style="width:17.5%">
      <col style="width:17.5%">
      <col style="width:17.5%">
    </colgroup>
    <tr>
      <td><strong>Detail</strong></td>
      <td><strong>Nilai Kiriman</strong></td>
      <td><strong>Pengirim</strong></td>
      <td><strong>Driver</strong></td>
      <td><strong>Penerima</strong></td>
    </tr>
    <tr>
      <td>Koli : {{ $shipment->koli_count ?? 1 }}</td>
      <td>Harga/Kg : {{ number_format((float)($shipment->rate?->price ?? 0),2) }}</td>
      <td class="sign" rowspan="4"></td>
      <td class="sign" rowspan="4"></td>
      <td class="sign" rowspan="4"></td>
    </tr>
    <tr>
      @php $chg = max((float)($shipment->weight_actual ?? 0), (float)($shipment->volume_weight ?? 0)); @endphp
      <td>Berat : {{ number_format((float)$shipment->weight_actual,2) }} kg (Chg: {{ number_format($chg,2) }} kg)</td>
      <td>Packing : {{ number_format((float)($shipment->packing_fee ?? 0),2) }}</td>
    </tr>
    <tr>
      <td>Volume : {{ number_format((float)$shipment->volume_weight,2) }} kg</td>
      <td>Asuransi : {{ number_format((float)($shipment->insurance_fee ?? 0),2) }}</td>
    </tr>
    <tr>
      <td>Lain-lain : {{ number_format((float)($shipment->other_fee ?? 0),2) }}</td>
      <td>Total : <strong>{{ number_format((float)($shipment->total_cost ?? 0),2) }}</strong></td>
    </tr>
  </table>
  <br>
  <div class="small muted mb-14">Rute: {{ $shipment->origin?->city }} → {{ $shipment->destination?->city }} | Status: {{ $shipment->status }}</div>

  <button class="no-print" onclick="window.print()" style="margin-top:10px; padding:6px 10px;">Print</button>
</body>
</html>
