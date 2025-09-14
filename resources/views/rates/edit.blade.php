@extends('layouts.app')
@section('title','Edit Rate')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit Rate</h1>
  <div>
    <x-help-button id="help-rates-edit" title="Bantuan • Edit Rate">
      <ul>
        <li><strong>Price</strong>/<strong>Lead Time</strong>/<strong>Active</strong>: sesuaikan tarif, estimasi, dan status aktif.</li>
        <li><strong>Validasi</strong>: kombinasi Origin/Destination/Service harus unik.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('rates.index') }}" class="btn btn-outline-secondary ml-2">Back</a>
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">Rate {{ session('status') }}.</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('rates._form')
</div></div>
@endsection

