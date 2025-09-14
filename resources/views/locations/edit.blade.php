@extends('layouts.app')
@section('title','Edit Location')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit Location</h1>
  <div>
    <x-help-button id="help-locations-edit" title="Bantuan • Edit Location">
      <ul>
        <li><strong>City/Province/Country</strong>: perbarui sesuai data resmi. Contoh: Balikpapan / Kalimantan Timur / Indonesia.</li>
        <li><strong>Validasi</strong>: kombinasi City+Province+Country tidak boleh duplikat.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary ml-2">Back</a>
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">Location {{ session('status') }}.</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('locations._form')
</div></div>
@endsection
