@extends('layouts.app')
@section('title','Payments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Payments</h1>
  <div>
    <x-help-button id="help-payments-index">
      <ul>
        <li>Filter berdasarkan Invoice No/Ref, Method, dan tanggal.</li>
        <li>Klik Add Payment untuk menambah pembayaran baru.</li>
        <li>Hapus payment akan menyesuaikan status invoice otomatis.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('payments.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> Add Payment</a>
  </div>
</div>

<form method="get" class="mb-3">
  <div class="form-row align-items-end">
    <div class="col-md-3">
      <label>Invoice No / Ref</label>
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search invoice no or ref">
    </div>
    <div class="col-md-2">
      <label>Method</label>
      <select name="method" class="form-control select2">
        <option value="">All</option>
        @foreach($methods as $m)
          <option value="{{ $m }}" {{ ($method ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
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
  <div class="alert alert-success">Payment {{ session('status') }}.</div>
@endif

<x-ui.table title="All Payments" :headers="['Invoice','Paid Amount','Paid Date','Method','Ref','Actions']">
  @forelse($payments as $p)
    <tr>
      <td>{{ $p->invoice?->invoice_no }}</td>
      <td>{{ number_format((float)$p->paid_amount,2) }}</td>
      <td>{{ $p->paid_date?->format('Y-m-d') }}</td>
      <td>{{ $p->method }}</td>
      <td>{{ $p->ref_no }}</td>
      <td>
        <a href="{{ route('payments.edit',$p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <form action="{{ route('payments.destroy',$p) }}" method="POST" class="d-inline" data-confirm="Hapus pembayaran ini?">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="6" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $payments->links() }}
@endsection
