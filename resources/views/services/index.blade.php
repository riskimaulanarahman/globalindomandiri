@extends('layouts.app')
@section('title','Services')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Services</h1>
  <div>
    <x-help-button id="help-services-index">
      <ul>
        <li>Kelola referensi layanan dan default Terms & Conditions.</li>
        <li>Terms & Conditions akan otomatis mengisi Quotation saat memilih service.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('services.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> New Service</a>
  </div>
  </div>

<form method="get" class="mb-3">
  <div class="form-row align-items-end">
    <div class="col-md-4">
      <label>Search</label>
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Name or Code">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">Filter</button>
    </div>
  </div>
</form>

<x-ui.table title="All Services" :headers="['Name','Code','Active','Actions']">
  @forelse($services as $svc)
    <tr>
      <td>{{ $svc->name }}</td>
      <td>{{ $svc->code }}</td>
      <td><span class="badge badge-{{ $svc->is_active ? 'success' : 'secondary' }}">{{ $svc->is_active ? 'Yes' : 'No' }}</span></td>
      <td>
        <a href="{{ route('services.edit',$svc) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <form action="{{ route('services.destroy',$svc) }}" method="POST" class="d-inline" data-confirm="Hapus service ini?"><input type="hidden" name="_token" value="{{ csrf_token() }}">@method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
      </td>
    </tr>
  @empty
    <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $services->links() }}
@endsection

