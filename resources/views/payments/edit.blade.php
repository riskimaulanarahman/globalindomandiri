@extends('layouts.app')
@section('title','Edit Payment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit Payment</h1>
  <div>
    <x-help-button id="help-payments-edit" title="Bantuan • Edit Payment">
      <ul>
        <li><strong>Invoice</strong>: boleh dipindah ke invoice lain bila salah input.</li>
        <li><strong>Paid Amount/Date/Method/Reference</strong>: perbarui sesuai bukti pembayaran.</li>
        <li>Sesudah simpan, status invoice akan terhitung otomatis.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary ml-2">Back</a>
  </div>
</div>
@if (session('status'))
  <div class="alert alert-success">Payment {{ session('status') }}.</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<div class="card shadow mb-4"><div class="card-body">
  @include('payments._form')
</div></div>
@endsection
