@extends('layouts.app')
@section('title','Create Shipment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Shipment</h1>
  <x-help-button id="help-shipments-create" title="Bantuan • Create Shipment">
    <ul>
      <li><strong>Resi No</strong>: biarkan kosong untuk auto-generate. Contoh: RGM-925-000123.</li>
      <li><strong>Customer</strong>: pilih pelanggan pengirim tagihan.</li>
      <li><strong>Origin/Destination</strong>: kota asal & tujuan.</li>
      <li><strong>Service Type</strong>: jenis layanan. Contoh: REG, EXP.</li>
      <li><strong>Rate</strong> (opsional): pilih rate agar <em>Base Fare</em> otomatis dihitung = rate.price × <em>chargeable</em>.</li>
      <li><strong>Weight/Volume</strong>: isi keduanya bila ada; sistem pakai yang terbesar (chargeable). Contoh: 12.5 / 10.0 (kg).</li>
      <li><strong>Koli</strong>: jumlah paket. Contoh: 3.</li>
      <li><strong>Sender/Receiver</strong>: isi <em>name</em>, <em>address</em>, <em>PIC</em>, <em>phone</em> sesuai dokumen.</li>
      <li><strong>Dates</strong>: Departed At (tanggal berangkat) & Received At (terisi saat diterima).</li>
      <li><strong>Cost breakdown</strong>: Base Fare, Packing, Insurance, Other, Discount, PPN, PPh23. Total dihitung otomatis saat simpan.</li>
      <li><strong>Status</strong>: Draft → Booked → In Transit → Delivered/Cancelled.</li>
    </ul>
  </x-help-button>
</div>
<div class="card shadow mb-4"><div class="card-body">
  @include('shipments._form')
</div></div>
@endsection
