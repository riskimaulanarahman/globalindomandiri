@extends('layouts.app')
@section('title','Locations')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Locations</h1>
  <div>
    <x-help-button id="help-locations-index" title="Bantuan • Locations">
      <ul>
        <li><strong>Pencarian</strong>: isi kata kunci City/Province/Country lalu <em>Search</em>.</li>
        <li><strong>Add Location</strong>: isi City & Country; Province opsional.</li>
        <li><strong>Penghapusan</strong>: hanya bila tidak dipakai oleh Rates/Shipments.</li>
        <li><strong>Contoh</strong>: City <em>Balikpapan</em>, Province <em>Kalimantan Timur</em>, Country <em>Indonesia</em>.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('locations.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> Add Location</a>
  </div>
</div>

<form method="get" class="mb-3">
  <div class="form-row">
    <div class="col-md-4">
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search city, province, country">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">Search</button>
    </div>
  </div>
</form>

@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
@if (session('status'))
  <div class="alert alert-success">{{ ucfirst(session('status')) }}.</div>
@endif

<x-ui.table title="All Locations" :headers="['City','Province','Country','Actions']">
  @forelse($locations as $l)
    <tr>
      <td>{{ $l->city }}</td>
      <td>{{ $l->province }}</td>
      <td>{{ $l->country }}</td>
      <td>
        <a href="{{ route('locations.edit',$l) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <form action="{{ route('locations.destroy',$l) }}" method="POST" class="d-inline" data-confirm="Hapus lokasi ini?">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $locations->links() }}
@endsection
