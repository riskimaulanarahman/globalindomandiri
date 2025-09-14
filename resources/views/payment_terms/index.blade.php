@extends('layouts.app')
@section('title','Payment Terms')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Payment Terms</h1>
  <div>
    <x-help-button id="help-payment-terms-index">
      <ul>
        <li>Kelola referensi Payment Terms yang dipakai di Quotation.</li>
        <li>Aktif/nonaktif untuk membatasi pilihan pada form.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('payment-terms.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> New Payment Term</a>
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

<x-ui.table title="All Payment Terms" :headers="['Name','Code','Active','Actions']">
  @forelse($terms as $t)
    <tr>
      <td>{{ $t->name }}</td>
      <td>{{ $t->code }}</td>
      <td><span class="badge badge-{{ $t->is_active ? 'success' : 'secondary' }}">{{ $t->is_active ? 'Yes' : 'No' }}</span></td>
      <td>
        <a href="{{ route('payment-terms.edit',$t) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <form action="{{ route('payment-terms.destroy',$t) }}" method="POST" class="d-inline" data-confirm="Hapus payment term ini?"><input type="hidden" name="_token" value="{{ csrf_token() }}">@method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
      </td>
    </tr>
  @empty
    <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
  @endforelse
</x-ui.table>

{{ $terms->links() }}
@endsection
