@extends('layouts.app')
@section('title','Customers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Customers</h1>
  <div>
    <x-help-button id="help-customers-index" title="Bantuan • Customers">
      <ul>
        <li><strong>Pencarian</strong>: ketik code/name lalu klik <em>Search</em>.</li>
        <li><strong>Add Customer</strong>: isi code & name wajib.</li>
        <li><strong>Edit/Delete</strong>: gunakan tombol pada kolom Actions. Hapus membutuhkan konfirmasi.</li>
        <li><strong>Contoh</strong>: code <em>CUST-001</em>, name <em>PT Contoh</em>, email <em>admin@contoh.co.id</em>.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('customers.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> Add Customer</a>
  </div>
</div>

<form method="get" class="mb-3">
  <div class="form-row">
    <div class="col-md-4">
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search code or name">
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

<x-ui.table title="All Customers" :headers="['Code','Name','Actions']">
  @forelse($customers as $c)
    <tr>
      <td>{{ $c->code }}</td>
      <td>{{ $c->name }}</td>
      <td>
        <a href="{{ route('customers.edit',$c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <form action="{{ route('customers.destroy',$c) }}" method="POST" class="d-inline" data-confirm="Hapus customer ini?">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $customers->links() }}
@endsection
