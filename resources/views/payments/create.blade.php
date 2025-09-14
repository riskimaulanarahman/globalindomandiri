@extends('layouts.app')
@section('title','Add Payment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Add Payment</h1>
  <x-help-button id="help-payments-create" title="Bantuan • Add Payment">
    <ul>
      <li><strong>Invoice</strong>: pilih invoice tujuan pembayaran.</li>
      <li><strong>Paid Amount</strong>: nominal ≤ outstanding. Contoh: 2500000.</li>
      <li><strong>Paid Date</strong>: tanggal bayar. Contoh: 2025-09-04.</li>
      <li><strong>Method</strong>: Transfer/Cash/Credit Card/Other.</li>
      <li><strong>Reference</strong>: (opsional) no. bukti transfer/ket.</li>
      <li>Status invoice akan diperbarui otomatis (Partially Paid/Paid).</li>
    </ul>
  </x-help-button>
</div>
<div class="card shadow mb-4"><div class="card-body">
  @include('payments._form')
</div></div>
@endsection
