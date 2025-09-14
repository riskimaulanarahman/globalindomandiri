@extends('layouts.app')
@section('title','Edit Quotation')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit Quotation</h1>
  <div class="d-flex align-items-center">
    <x-help-button id="help-quotes-edit" class="mr-2" title="Bantuan • Edit Quotation">
      <ul>
        <li><strong>Header</strong>: Date, Valid Until, Customer, Route, Service/Lead Time, Currency/Tax/Discount, Payment Terms, <strong>Terms & Conditions</strong> (pilih dari modul T&C berdasarkan Service).</li>
        <li><strong>Items</strong>:
          <br>- Manual: Description, Qty, UOM, Unit Price → Amount otomatis.
          <br>- Add from Rate: pilih rate sesuai route untuk mengisi harga.</li>
        <li><strong>Status</strong>: Draft → Sent → Accepted/Rejected/Expired/Converted.</li>
        <li><strong>Convert</strong>: buat Shipment (Draft) dari quotation Accepted.</li>
        <li><strong>Print</strong>: gunakan tombol Print untuk cetak/arsip PDF dari browser.</li>
      </ul>
    </x-help-button>
    @if($quotation->status === 'Draft')
      <form action="{{ route('quotations.markSent',$quotation) }}" method="POST" class="mr-2" data-confirm="Tandai quotation ini sebagai 'Sent'?"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn btn-warning">Mark as Sent</button></form>
    @endif
    @if(in_array($quotation->status,['Draft','Sent']))
      <form action="{{ route('quotations.accept',$quotation) }}" method="POST" class="mr-2" data-confirm="Terima quotation ini?"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn btn-success">Accept</button></form>
      <form action="{{ route('quotations.reject',$quotation) }}" method="POST" class="mr-2" data-confirm="Tolak quotation ini?"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn btn-danger">Reject</button></form>
    @endif
    @if($quotation->status === 'Accepted')
      <form action="{{ route('quotations.convert',$quotation) }}" method="POST" class="mr-2" data-confirm="Buat Shipment dari quotation ini?">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-primary" {{ ($quotation->valid_until && now()->gt($quotation->valid_until)) ? 'disabled' : '' }}>Create Shipment</button>
      </form>
      <form action="{{ route('quotations.close',$quotation) }}" method="POST" class="mr-2" data-confirm="Tutup quotation ini (tidak bisa dipakai lagi)?">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-outline-secondary">Close Quote</button>
      </form>
    @endif
    <a href="{{ route('quotations.print',$quotation) }}" target="_blank" class="btn btn-outline-secondary mr-2"><i class="fas fa-print mr-1"></i> Print</a>
    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">{{ ucfirst(str_replace('-',' ',session('status'))) }}.</div>
@endif
@if (session('warning'))
  <div class="alert alert-warning">{{ session('warning') }}</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('quotations._header_form')
</div></div>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Items</h6>
  </div>
  <div class="card-body">
    @if(!in_array($quotation->status, ['Rejected','Expired','Converted','Closed']))
    <form action="{{ route('quotations.lines.add',$quotation) }}" method="post" class="mb-3">
      @csrf
      <div class="form-row">
        <div class="form-group col-md-3">
          <label class="small mb-1">Origin</label>
          <select name="origin_id" class="form-control select2" data-placeholder="Origin">
            <option value="">Select origin</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}">{{ $loc->city }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-3">
          <label class="small mb-1">Destination</label>
          <select name="destination_id" class="form-control select2" data-placeholder="Destination">
            <option value="">Select destination</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}">{{ $loc->city }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-3">
          <label class="small mb-1">Service</label>
          <select name="service_type" class="form-control select2" data-placeholder="Service">
            <option value="">Select service</option>
            @foreach(($services ?? []) as $svc)
              <option value="{{ $svc->name }}">{{ $svc->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1">
          <label class="small mb-1">Min Kg</label>
          <input type="number" step="0.01" min="0" name="min_weight" class="form-control" placeholder="10">
        </div>
        <div class="form-group col-md-2">
          <label class="small mb-1">Unit Price</label>
          <input type="number" step="0.01" min="0" name="unit_price" class="form-control" placeholder="0" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-3">
          <label class="small mb-1">Lead time</label>
          <select id="lead_time_select" class="form-control">
            <option value="">Select...</option>
            <option value="3-5 days">3–5 days</option>
            <option value="5-7 days">5–7 days</option>
            <option value="7-10 days">7–10 days</option>
            <option value="10-14 days">10–14 days</option>
            <option value="Same day">Same day</option>
            <option value="custom">Custom…</option>
          </select>
        </div>
        <div class="form-group col-md-4" id="lead_time_custom_group" style="display:none;">
          <label class="small mb-1">Custom lead time</label>
          <input type="text" id="lead_time_custom" class="form-control" placeholder="e.g. 5-7 days">
        </div>
        <div class="form-group col-md-3">
          <label class="small mb-1">Remarks</label>
          <input type="text" name="remarks" class="form-control" placeholder="e.g. Udara min. 10kg">
        </div>
        <div class="form-group col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-outline-primary btn-block"><i class="fas fa-plus mr-1"></i> Add</button>
        </div>
      </div>
      <input type="hidden" name="lead_time" id="lead_time_value">
    </form>
    <form action="{{ route('quotations.lines.add',$quotation) }}" method="post" class="mb-3">
      @csrf
      <div class="form-row align-items-end">
        <div class="form-group col-md-9">
          <label class="small mb-1">Add from Rate</label>
          <select id="rate_picker" class="form-control select2" data-placeholder="Add from Rate">
            <option value="">-- Add from Rate --</option>
            @foreach(($rates ?? []) as $r)
              <option value="{{ $r->id }}"
                data-origin-id="{{ $r->origin_id }}"
                data-destination-id="{{ $r->destination_id }}"
                data-service-type="{{ $r->service_type }}"
                data-lead-time="{{ $r->lead_time }}"
                data-min-weight="{{ (int)($r->min_weight ?? 0) }}"
                data-max-weight="{{ (int)($r->max_weight ?? 0) }}"
                data-desc="{{ $r->origin?->city }} → {{ $r->destination?->city }} | {{ $r->service_type }}"
                data-price="{{ (float)$r->price }}">
                {{ $r->origin?->city }} → {{ $r->destination?->city }} | {{ $r->service_type }} | {{ number_format((float)$r->price,2) }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-3">
          <button type="submit" class="btn btn-outline-success btn-block" id="add_from_rate" disabled>
            <i class="fas fa-plus mr-1"></i> Add from Rate
          </button>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-2">
          <label class="small mb-1">Qty</label>
          <input type="number" step="1" min="1" name="qty" id="rate_qty" class="form-control" value="1">
        </div>
        <div class="form-group col-md-3">
          <label class="small mb-1">UOM</label>
          <input type="text" name="uom" id="rate_uom" class="form-control" list="uom_options" placeholder="e.g. Kg, Trip, Colly">
        </div>
        <div class="form-group col-md-4">
          <label class="small mb-1">Unit Price</label>
          <input type="number" step="0.01" min="0" name="unit_price" id="rate_price" class="form-control" placeholder="0">
        </div>
      </div>
      <input type="hidden" name="origin_id" id="rate_origin_id">
      <input type="hidden" name="destination_id" id="rate_destination_id">
      <input type="hidden" name="service_type" id="rate_service_type">
      <input type="hidden" name="lead_time" id="rate_lead_time">
      <input type="hidden" name="min_weight" id="rate_min_weight">
      <input type="hidden" name="description" id="rate_desc">
      <input type="hidden" name="remarks" id="rate_remarks">
      <input type="hidden" name="item_type" value="route">
    </form>
    <datalist id="uom_options">
      <option value="Kg"></option>
      <option value="Trip"></option>
      <option value="Colly"></option>
    </datalist>

    <form action="{{ route('quotations.lines.add',$quotation) }}" method="post" class="mb-4">
      @csrf
      <div class="form-row align-items-end">
        <div class="form-group col-md-4">
          <label class="small mb-1">Custom Item — Description</label>
          <input type="text" name="description" class="form-control" placeholder="e.g. Packing kayu, Asuransi, Handling, dll">
        </div>
        <div class="form-group col-md-2">
          <label class="small mb-1">Qty</label>
          <input type="number" step="1" min="1" name="qty" class="form-control" value="1">
        </div>
        <div class="form-group col-md-2">
          <label class="small mb-1">UOM</label>
          <input type="text" name="uom" class="form-control" list="uom_options" placeholder="e.g. Kg, Trip, Colly">
        </div>
        <div class="form-group col-md-2">
          <label class="small mb-1">Unit Price</label>
          <input type="number" step="0.01" min="0" name="unit_price" class="form-control" placeholder="0">
        </div>
        <div class="form-group col-md-2">
          <button type="submit" class="btn btn-outline-dark btn-block">
            <i class="fas fa-plus mr-1"></i> Add Custom Item
          </button>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label class="small mb-1">Remarks (optional)</label>
          <input type="text" name="remarks" class="form-control" placeholder="Optional notes for this item">
        </div>
        <div class="form-group col-md-3">
          <label class="small mb-1">Lead time (optional)</label>
          <input type="text" name="lead_time" class="form-control" placeholder="e.g. Same day">
        </div>
      </div>
      <input type="hidden" name="item_type" value="custom">
    </form>
    @else
      <div class="text-muted">Quotation berstatus <strong>{{ $quotation->status }}</strong>. Penambahan item dinonaktifkan.</div>
    @endif
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead><tr><th>No</th><th>Description</th><th class="text-right">Price</th><th>Remarks</th><th>Leadtime</th><th>Actions</th></tr></thead>
        <tbody>
          @forelse(($lines ?? $quotation->lines) as $line)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $line->origin?->city ? ($line->origin?->city.' → '.$line->destination?->city) : $line->description }}</td>
              <td class="text-right">
                {{ number_format((float)$line->unit_price,2) }}@if($line->uom) /{{ ucfirst(strtolower($line->uom)) }}@endif
              </td>
              <td>
                @php
                  $remarksFallback = trim(($line->service_type ? $line->service_type : '').($line->min_weight ? ' min. '.(int)$line->min_weight.'kg' : ''));
                @endphp
                @if(!in_array($quotation->status, ['Rejected','Expired','Converted','Closed']))
                  <form action="{{ route('quotations.lines.update', [$quotation,$line]) }}" method="POST" class="form-inline">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="remarks" class="form-control form-control-sm mr-2" style="min-width:220px" value="{{ old('remarks', $line->remarks ?? $remarksFallback) }}" placeholder="e.g. Udara min. 10kg">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Save</button>
                  </form>
                @else
                  {{ $line->remarks ?: ($remarksFallback ?: '—') }}
                @endif
              </td>
              <td>{{ $line->lead_time ?: '—' }}</td>
              <td>
                <form action="{{ route('quotations.lines.createShipment', [$quotation,$line]) }}" method="POST" class="d-inline" data-confirm="Buat shipment dari baris ini?">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-primary" {{ ($quotation->status !== 'Accepted' || ($quotation->valid_until && now()->gt($quotation->valid_until)) || !$line->origin_id || !$line->destination_id) ? 'disabled' : '' }}>Create Shipment</button>
                </form>
                <form action="{{ route('quotations.lines.remove', [$quotation,$line]) }}" method="POST" class="d-inline" data-confirm="Hapus item ini?"><input type="hidden" name="_token" value="{{ csrf_token() }}">@method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No items</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">
      Related Shipments
      <span class="badge badge-secondary">{{ count($relatedShipments ?? []) }}</span>
    </h6>
    @if($quotation->status === 'Accepted')
      <form action="{{ route('quotations.convert',$quotation) }}" method="POST" class="mb-0" data-confirm="Buat Shipment dari quotation ini?">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-sm btn-primary" {{ ($quotation->valid_until && now()->gt($quotation->valid_until)) ? 'disabled' : '' }}>
          <i class="fas fa-plus mr-1"></i> Create Shipment
        </button>
      </form>
    @endif
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Resi</th>
            <th>Customer</th>
            <th>Route</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse(($relatedShipments ?? []) as $s)
            <tr>
              <td>{{ $s->resi_no ?: '—' }}</td>
              <td>{{ $s->customer?->name }}</td>
              <td>{{ $s->origin?->city }} → {{ $s->destination?->city }}</td>
              <td><span class="badge badge-{{ $s->status === 'Delivered' ? 'success' : ($s->status === 'Cancelled' ? 'secondary' : 'info') }}">{{ $s->status }}</span></td>
              <td>
                <a href="{{ route('shipments.edit',$s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="{{ route('shipments.awb',$s) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Print Resi</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted">No related shipments</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
  $(function(){
    const $picker = $('#rate_picker');
    const $btn = $('#add_from_rate');
    const $desc = $('#rate_desc');
    const $price = $('#rate_price');
    const $o = $('#rate_origin_id');
    const $d = $('#rate_destination_id');
    const $svc = $('#rate_service_type');
    const $lt = $('#rate_lead_time');
    const $mw = $('#rate_min_weight');
    const $remarks = $('#rate_remarks');
    const $qty = $('#rate_qty');
    const $uom = $('#rate_uom');

    // Lead time (manual form): select + custom -> hidden value
    const $ltSel = $('#lead_time_select');
    const $ltCustom = $('#lead_time_custom');
    const $ltCustomGroup = $('#lead_time_custom_group');
    const $ltVal = $('#lead_time_value');
    function syncLeadTime(){
      const sel = $ltSel.val();
      if (sel === 'custom') {
        $ltCustomGroup.show();
        $ltVal.val($ltCustom.val());
      } else {
        $ltCustomGroup.hide();
        $ltVal.val(sel || '');
      }
    }
    $ltSel.on('change', syncLeadTime);
    $ltCustom.on('input', syncLeadTime);
    syncLeadTime();

    $picker.on('change', function(){
      const opt = this.options[this.selectedIndex];
      if (opt && opt.value) {
        $desc.val(opt.dataset.desc || 'Rate item');
        $price.val(opt.dataset.price || 0);
        $o.val(opt.dataset.originId || '');
        $d.val(opt.dataset.destinationId || '');
        $svc.val(opt.dataset.serviceType || '');
        $lt.val(opt.dataset.leadTime || '');
        $mw.val(opt.dataset.minWeight || '');
        // build default remarks: service_type + min + optional max
        const minKg = $mw.val();
        const maxKg = opt.dataset.maxWeight;
        let rmk = '';
        if (opt.dataset.serviceType) rmk += opt.dataset.serviceType;
        if (minKg && parseInt(minKg,10) > 0) rmk += (rmk ? ' ' : '') + 'min. ' + parseInt(minKg,10) + 'kg';
        if (maxKg && parseInt(maxKg,10) > 0) rmk += (rmk ? ' ' : '') + 'max. ' + parseInt(maxKg,10) + 'kg';
        $remarks.val(rmk);
        // default qty/uom
        $qty.val(1);
        const svc = (opt.dataset.serviceType || '').toLowerCase();
        if (svc.includes('charter')) {
          $uom.val('Trip');
        } else if (svc.includes('express')) {
          $uom.val('Colly');
        } else {
          $uom.val('Kg');
        }
        $btn.prop('disabled', false);
      } else {
        $btn.prop('disabled', true);
        $desc.val('');
        $price.val('');
        $o.val(''); $d.val(''); $svc.val(''); $lt.val('');
        $remarks.val(''); $qty.val(1); $uom.val('');
      }
    });
  });
</script>
@endpush
