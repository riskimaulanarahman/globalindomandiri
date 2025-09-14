@extends('layouts.app')
@section('title','Create Quotation')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Quotation</h1>
  <x-help-button id="help-quotes-create" title="Bantuan • Create Quotation">
    <ul>
      <li><strong>Date</strong> & <strong>Valid Until</strong>: periode berlaku penawaran. Contoh: berlaku 14 hari.</li>
      <li><strong>Customer</strong> & <strong>PIC/Phone</strong>: penerima penawaran.</li>
      <li><strong>Route</strong> & <strong>Service/Lead Time</strong>: rute & layanan yang ditawarkan. Pilih <strong>Terms & Conditions</strong> dari modul T&C (berdasarkan Service).</li>
      <li><strong>Currency</strong>, <strong>Tax %</strong>, <strong>Discount</strong>: parameter perhitungan total.</li>
      <li><strong>Payment Terms</strong>: syarat pembayaran.</li>
      <li>Nomor quotation dibuat otomatis saat simpan; item ditambahkan di halaman Edit.</li>
    </ul>
  </x-help-button>
</div>

<div class="card shadow mb-4"><div class="card-body">
  @include('quotations._header_form')
</div></div>
@endsection
