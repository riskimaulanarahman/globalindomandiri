@extends('layouts.app')
@section('title','Quotations')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Quotations</h1>
  <div>
    <x-help-button id="help-quotes-index">
      <ul>
        <li>Filter berdasarkan nomor, customer, status, dan tanggal.</li>
        <li>Klik Edit untuk mengelola header dan item.</li>
        <li>Gunakan Convert untuk membuat Shipment dari quotation Accepted.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('quotations.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> New Quotation</a>
  </div>
</div>

<form method="get" class="mb-3">
  <div class="form-row align-items-end">
    <div class="col-md-3">
      <label>No</label>
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Quote no">
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
      <input type="date" name="from" value="{{ $from }}" class="form-control">
    </div>
    <div class="col-md-2">
      <label>To</label>
      <input type="date" name="to" value="{{ $to }}" class="form-control">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">Filter</button>
    </div>
  </div>
</form>

<x-ui.table title="All Quotations" :headers="['No','Date','Customer','Status','Valid Until','Total','Actions']">
  @forelse($quotations as $qtn)
    <tr>
      <td>{{ $qtn->quote_no }}</td>
      <td>{{ $qtn->quote_date?->format('Y-m-d') }}</td>
      <td>{{ $qtn->customer?->name }}</td>
      {{-- <td>{{ $qtn->origin?->city }} → {{ $qtn->destination?->city }}</td> --}}
      <td><span class="badge badge-{{ $qtn->status === 'Accepted' ? 'success' : ($qtn->status === 'Rejected' ? 'danger' : 'secondary') }}">{{ $qtn->status }}</span></td>
      <td>{{ $qtn->valid_until?->format('Y-m-d') }}</td>
      <td>{{ number_format((float)$qtn->total,2) }}</td>
      <td>
        <a href="{{ route('quotations.edit',$qtn) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <a href="{{ route('quotations.print',$qtn) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Print</a>
      </td>
    </tr>
  @empty
    <tr><td colspan="8" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $quotations->links() }}
@endsection

