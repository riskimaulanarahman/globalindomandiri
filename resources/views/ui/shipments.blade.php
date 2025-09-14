@extends('layouts.app')
@section('title','Shipments')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Shipments</h1>
<x-ui.table title="Shipments" :headers="['Resi','Customer','Origin','Destination','Service','Status','Total']">
  @foreach(\App\Models\Shipment::with(['customer','origin','destination'])->latest()->limit(10)->get() as $s)
    <tr>
      <td>{{ $s->resi_no }}</td>
      <td>{{ $s->customer?->name }}</td>
      <td>{{ $s->origin?->city }}</td>
      <td>{{ $s->destination?->city }}</td>
      <td>{{ $s->service_type }}</td>
      <td><span class="badge badge-info">{{ $s->status }}</span></td>
      <td>{{ number_format((float)$s->total_cost,2) }}</td>
    </tr>
  @endforeach
</x-ui.table>
@endsection

