@extends('layouts.app')
@section('title','Create Invoice')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Invoice</h1>
  <x-help-button id="help-invoices-create" title="Bantuan • Create Invoice">
    <ul>
      <li><strong>Date</strong>: tanggal invoice. Contoh: 2025-09-04.</li>
      <li><strong>Due Date / TOP</strong>: isi Due Date atau TOP (hari). Contoh: TOP 14 → due date +14 hari.</li>
      <li><strong>Customer</strong>: wajib dipilih.</li>
      <li><strong>Terms</strong> & <strong>Remarks</strong>: ketentuan & catatan di invoice.</li>
      <li><strong>Nomor</strong>: dibuat otomatis saat simpan.</li>
      <li>Setelah simpan, tambahkan item pada halaman <em>Edit Invoice</em>.</li>
    </ul>
  </x-help-button>
</div>
<div class="card shadow mb-4"><div class="card-body">
  @include('invoices._header_form')
</div></div>
@endsection
