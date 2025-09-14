@extends('layouts.app')
@section('title','Edit Payment Term')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Edit Payment Term</h1>
</div>

@if (session('status'))
  <div class="alert alert-success">{{ ucfirst(str_replace('-',' ',session('status'))) }}.</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('payment_terms._form')
  </div></div>
@endsection
