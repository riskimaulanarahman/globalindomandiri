@extends('layouts.app')
@section('title','Invoices')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Invoices</h1>
  <div>
    <x-help-button id="help-invoices-index">
      <ul>
        <li>Filter berdasarkan nomor, customer, status, dan tanggal.</li>
        <li>Klik Edit untuk kelola header dan lines; klik Print untuk cetak.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> New Invoice</a>
  </div>
</div>

<form method="get" class="mb-3">
  <div class="form-row align-items-end">
    <div class="col-md-3">
      <label>Search No</label>
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Invoice no">
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

@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

<x-ui.table title="All Invoices" :headers="['No','Date','Customer','Due','Status','Total','Outstanding','Actions']">
  @forelse($invoices as $i)
    <tr>
      <td>{{ $i->invoice_no }}</td>
      <td>{{ $i->invoice_date?->format('Y-m-d') }}</td>
      <td>{{ $i->customer?->name }}</td>
      <td>{{ $i->due_date?->format('Y-m-d') }}</td>
      <td><span class="badge badge-{{ $i->status === 'Paid' ? 'success' : ($i->status === 'Overdue' ? 'danger' : 'secondary') }}">{{ $i->status }}</span></td>
      <td>{{ number_format((float)$i->total_amount,2) }}</td>
      <td>{{ number_format((float)$i->outstanding,2) }}</td>
      <td>
        <a href="{{ route('invoices.edit',$i) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <a href="{{ route('invoices.print',$i) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Print</a>
        @if($i->status === 'Draft' && !$i->payments->count())
          <form action="{{ route('invoices.destroy',$i) }}" method="POST" class="d-inline" data-confirm="Hapus invoice ini?">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        @endif
      </td>
    </tr>
  @empty
    <tr><td colspan="8" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $invoices->links() }}
@endsection
