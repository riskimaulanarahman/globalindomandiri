@extends('layouts.app')
@section('title','Create Service')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Service</h1>
</div>

<div class="card shadow mb-4"><div class="card-body">
  @include('services._form')
</div></div>
@endsection

