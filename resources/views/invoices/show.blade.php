@extends('layouts.app')
@section('title','Invoice '.$invoice->invoice_no)
@section('content')
<div class="card shadow mb-4">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div>
        <h4 class="mb-1">Invoice {{ $invoice->invoice_no }}</h4>
        <div>Date: {{ $invoice->invoice_date?->format('Y-m-d') }}</div>
        <div>Due: {{ $invoice->due_date?->format('Y-m-d') }}</div>
        <div>Status: {{ $invoice->status }}</div>
      </div>
      <div class="text-right">
        <img src="{{ asset('img/rrgm-logo.png') }}" alt="Logo" style="height:40px" />
        <div class="mt-2"><strong>{{ config('app.name') }}</strong></div>
      </div>
    </div>

    <div class="mb-3">
      <div><strong>Bill To:</strong></div>
      <div>{{ $invoice->customer?->name }}</div>
    </div>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Description</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Amount</th>
          <th class="text-right">Line Total</th>
        </tr>
      </thead>
      <tbody>
        @php $no=1; $sum=0; @endphp
        @foreach($invoice->lines as $line)
          @php $lt = (float)$line->qty * (float)$line->amount; $sum += $lt; @endphp
          <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $line->description }}</td>
            <td class="text-right">{{ number_format((float)$line->qty,2) }}</td>
            <td class="text-right">{{ number_format((float)$line->amount,2) }}</td>
            <td class="text-right">{{ number_format($lt,2) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4" class="text-right">Total</th>
          <th class="text-right">{{ number_format($sum,2) }}</th>
        </tr>
        <tr>
          <th colspan="4" class="text-right">Paid</th>
          <th class="text-right">{{ number_format((float)$invoice->paid_amount,2) }}</th>
        </tr>
        <tr>
          <th colspan="4" class="text-right">Outstanding</th>
          <th class="text-right">{{ number_format((float)$invoice->outstanding,2) }}</th>
        </tr>
      </tfoot>
    </table>

    @if($invoice->remarks)
      <div class="mt-3"><strong>Remarks:</strong> {{ $invoice->remarks }}</div>
    @endif
    @if($invoice->terms_text)
      <div class="mt-1"><strong>Terms:</strong> {{ $invoice->terms_text }}</div>
    @endif
  </div>
</div>
@endsection
