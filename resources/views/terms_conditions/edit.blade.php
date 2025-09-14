@extends('layouts.app')
@section('title','Edit T&C')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Edit Terms & Conditions</h1>
  <a href="{{ route('terms-conditions.index') }}" class="btn btn-secondary">Back</a>
</div>
<div class="card shadow"><div class="card-body">
  @include('terms_conditions._form')
</div></div>
@endsection

