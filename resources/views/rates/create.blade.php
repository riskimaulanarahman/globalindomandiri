@extends('layouts.app')
@section('title','Create Rate')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Rate</h1>
  <x-help-button id="help-rates-create" title="Bantuan • Create Rate">
    <ul>
      <li><strong>Origin</strong>: pilih lokasi asal. Contoh: Balikpapan.</li>
      <li><strong>Destination</strong>: pilih lokasi tujuan. Contoh: Jakarta.</li>
      <li><strong>Service Type</strong>: kode layanan. Contoh: REG, EXP.</li>
      <li><strong>Price</strong>: tarif per kg. Contoh: 8500.</li>
      <li><strong>Lead Time</strong>: estimasi waktu. Contoh: 2-3 days.</li>
      <li><strong>Active</strong>: centang untuk mengaktifkan rate.</li>
    </ul>
  </x-help-button>
</div>
<div class="card shadow mb-4"><div class="card-body">
  @include('rates._form')
</div></div>
@endsection
