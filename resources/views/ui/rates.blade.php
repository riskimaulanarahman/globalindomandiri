@extends('layouts.app')
@section('title','Rates')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Rates</h1>
<x-ui.table title="Rates" :headers="['Origin','Destination','Service','Price','Lead Time']">
  @foreach(\App\Models\Rate::with(['origin','destination'])->latest()->limit(10)->get() as $r)
    <tr>
      <td>{{ $r->origin?->city }}</td>
      <td>{{ $r->destination?->city }}</td>
      <td>{{ $r->service_type }}</td>
      <td>{{ number_format($r->price,2) }}</td>
      <td>{{ $r->lead_time }}</td>
    </tr>
  @endforeach
</x-ui.table>
@endsection

