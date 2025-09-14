@extends('layouts.app')
@section('title','Create Payment Term')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Payment Term</h1>
</div>

@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('payment_terms._form')
  </div></div>
@endsection
