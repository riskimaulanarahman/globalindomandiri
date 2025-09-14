@extends('layouts.app')
@section('title','Locations')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Locations</h1>
<x-ui.table title="Locations" :headers="['City','Province','Country']">
  @foreach(\App\Models\Location::latest()->limit(10)->get() as $l)
    <tr>
      <td>{{ $l->city }}</td>
      <td>{{ $l->province }}</td>
      <td>{{ $l->country }}</td>
    </tr>
  @endforeach
</x-ui.table>
@endsection

