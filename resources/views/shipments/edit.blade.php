@extends('layouts.app')
@section('title','Edit Shipment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit Shipment</h1>
  <div>
    <a href="{{ route('shipments.awb',$shipment) }}" target="_blank" class="btn btn-outline-secondary mr-2"><i class="fas fa-print mr-1"></i> Print Resi</a>
    <a href="{{ route('shipments.awb_barcode',$shipment) }}" target="_blank" class="btn btn-outline-secondary mr-2"><i class="fas fa-barcode mr-1"></i> Print Resi + Barcode</a>
    @if($shipment->invoiceLine)
      <a href="{{ route('invoices.edit', $shipment->invoiceLine->invoice_id) }}" class="btn btn-success mr-2"><i class="fas fa-file-invoice mr-1"></i> View Invoice</a>
    @else
      <form action="{{ route('shipments.createInvoice', $shipment) }}" method="POST" class="d-inline mr-2" data-confirm="Buat invoice untuk shipment ini?">
        @csrf
        <button type="submit" class="btn btn-outline-success"><i class="fas fa-file-invoice mr-1"></i> Create Invoice</button>
      </form>
    @endif
    <x-help-button id="help-shipments-edit" title="Bantuan • Edit Shipment">
      <ul>
        <li><strong>Resi No</strong>: ubah jika perlu; biarkan format konsisten.</li>
        <li><strong>Customer/Route/Service</strong>: sesuaikan data aktual pengiriman.</li>
        <li><strong>Berat</strong>: isi Actual & Volume; sistem hitung chargeable (maksimal).</li>
        <li><strong>Rate & Base Fare</strong>: pilih rate lalu periksa Base Fare; bisa dioverride.</li>
        <li><strong>Biaya</strong>: Packing/Insurance/Other/Discount/PPN/PPh23 → Total dihitung otomatis.</li>
        <li><strong>Sender/Receiver</strong>: isi lengkap (name/address/PIC/phone).</li>
        <li><strong>Status</strong>: ubah sesuai progress (Draft/Booked/In Transit/Delivered/Cancelled).</li>
      </ul>
    </x-help-button>
    <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary ml-2">Back</a>
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">Shipment {{ session('status') }}.</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('shipments._form')
</div></div>
@endsection
