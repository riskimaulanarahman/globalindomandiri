@extends('layouts.app')
@section('title','Invoices')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Invoices</h1>
<x-ui.table title="Invoices" :headers="['Invoice No','Date','Customer','Due','Status','Total','Outstanding']">
  @foreach(\App\Models\Invoice::with('customer','payments')->latest()->limit(10)->get() as $i)
    @php $paid = (float)$i->payments->sum('paid_amount'); $out = max(0,(float)$i->total_amount - $paid); @endphp
    <tr>
      <td>{{ $i->invoice_no }}</td>
      <td>{{ $i->invoice_date }}</td>
      <td>{{ $i->customer?->name }}</td>
      <td>{{ $i->due_date }}</td>
      <td><span class="badge badge-secondary">{{ $i->status }}</span></td>
      <td>{{ number_format((float)$i->total_amount,2) }}</td>
      <td>{{ number_format($out,2) }}</td>
    </tr>
  @endforeach
</x-ui.table>
@endsection

