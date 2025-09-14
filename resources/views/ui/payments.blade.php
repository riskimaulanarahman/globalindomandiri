@extends('layouts.app')
@section('title','Payments')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Payments</h1>
<x-ui.table title="Payments" :headers="['Invoice','Paid Amount','Paid Date','Method','Ref']">
  @foreach(\App\Models\Payment::with('invoice')->latest()->limit(10)->get() as $p)
    <tr>
      <td>{{ $p->invoice?->invoice_no }}</td>
      <td>{{ number_format((float)$p->paid_amount,2) }}</td>
      <td>{{ $p->paid_date }}</td>
      <td>{{ $p->method }}</td>
      <td>{{ $p->ref_no }}</td>
    </tr>
  @endforeach
</x-ui.table>
@endsection

