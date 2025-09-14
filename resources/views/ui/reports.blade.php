@extends('layouts.app')
@section('title','Reports')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Reports</h1>
<div class="row">
  <div class="col-xl-6 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2"><div class="card-body">
      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Shipments by Status</div>
      <div class="h6 text-gray-800">Draft: {{ \App\Models\Shipment::where('status','Draft')->count() }}, Delivered: {{ \App\Models\Shipment::where('status','Delivered')->count() }}</div>
    </div></div>
  </div>
  <div class="col-xl-6 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2"><div class="card-body">
      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">AR Aging (simple)</div>
      <div class="h6 text-gray-800">Total Outstanding: {{ number_format(\App\Models\Invoice::sum('total_amount') - \App\Models\Payment::sum('paid_amount'),2) }}</div>
    </div></div>
  </div>
</div>
@endsection

