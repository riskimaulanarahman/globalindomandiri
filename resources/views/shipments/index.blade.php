@extends('layouts.app')
@section('title','Shipments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Shipments</h1>
  <div>
    <x-help-button id="help-shipments-index">
      <ul>
        <li>Filter berdasarkan Resi, Customer, Status, dan rentang tanggal.</li>
        <li>Klik Add Shipment untuk membuat shipment baru.</li>
        <li>Hapus diblokir jika shipment sudah masuk invoice.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('shipments.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> Add Shipment</a>
  </div>
</div>

<form method="get" class="mb-3">
  <div class="form-row align-items-end">
    <div class="col-md-3">
      <label>Search Resi</label>
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Resi no">
    </div>
    <div class="col-md-3">
      <label>Customer</label>
      <select name="customer_id" class="form-control select2">
        <option value="">All</option>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ (int)($customerId ?? 0) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label>Status</label>
      <select name="status" class="form-control select2">
        <option value="">All</option>
        @foreach($statuses as $s)
          <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label>From</label>
      <input type="date" name="from" value="{{ $fromDate }}" class="form-control">
    </div>
    <div class="col-md-2">
      <label>To</label>
      <input type="date" name="to" value="{{ $toDate }}" class="form-control">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">Filter</button>
    </div>
  </div>
</form>

@if (session('status'))
  <div class="alert alert-success">Shipment {{ session('status') }}.</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<x-ui.table title="All Shipments" :headers="['Resi','Customer','Route','Service','Weight (chg)','Total','Status','Actions']">
  @forelse($shipments as $s)
    <tr>
      <td>{{ $s->resi_no }}</td>
      <td>{{ $s->customer?->name }}</td>
      <td>{{ $s->origin?->city }} → {{ $s->destination?->city }}</td>
      <td>{{ $s->service_type }}</td>
      <td>{{ number_format((float)max($s->weight_actual,$s->volume_weight),2) }}</td>
      <td>{{ number_format((float)$s->total_cost,2) }}</td>
      <td><span class="badge badge-{{ $s->status === 'Delivered' ? 'success' : ($s->status === 'Cancelled' ? 'secondary' : 'info') }}">{{ $s->status }}</span></td>
      <td>
        <a href="{{ route('shipments.awb',$s) }}" target="_blank" class="btn btn-sm btn-outline-secondary mr-1">Print Resi</a>
        <a href="{{ route('shipments.edit',$s) }}" class="btn btn-sm btn-outline-primary mr-1">Edit</a>
        @if($s->invoiceLine)
          <a href="{{ route('invoices.edit', $s->invoiceLine->invoice_id) }}" class="btn btn-sm btn-success mr-1">View Invoice</a>
        @else
          <form action="{{ route('shipments.createInvoice', $s) }}" method="POST" class="d-inline mr-1" data-confirm="Buat invoice untuk shipment ini?">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success">Create Invoice</button>
          </form>
        @endif
        <form action="{{ route('shipments.destroy',$s) }}" method="POST" class="d-inline" data-confirm="Hapus shipment ini?">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="8" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $shipments->links() }}
@endsection
