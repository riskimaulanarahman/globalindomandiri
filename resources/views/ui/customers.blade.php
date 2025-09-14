@extends('layouts.app')
@section('title','Customers')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Customers</h1>
<x-ui.table title="Customers" :headers="['Code','Name']">
  @foreach(\App\Models\Customer::latest()->limit(10)->get() as $c)
    <tr>
      <td>{{ $c->code }}</td>
      <td>{{ $c->name }}</td>
    </tr>
  @endforeach
</x-ui.table>
@endsection

