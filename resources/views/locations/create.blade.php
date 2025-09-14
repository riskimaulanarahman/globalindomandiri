@extends('layouts.app')
@section('title','Create Location')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Location</h1>
  <x-help-button id="help-locations-create" title="Bantuan • Create Location">
    <ul>
      <li><strong>City</strong>: nama kota. Contoh: Balikpapan.</li>
      <li><strong>Province</strong>: (opsional) provinsi. Contoh: Kalimantan Timur.</li>
      <li><strong>Country</strong>: negara. Contoh: Indonesia.</li>
      <li><strong>Unik</strong>: kombinasi City+Province+Country tidak boleh duplikat.</li>
    </ul>
  </x-help-button>
</div>
<div class="card shadow mb-4"><div class="card-body">
  @include('locations._form')
</div></div>
@endsection
