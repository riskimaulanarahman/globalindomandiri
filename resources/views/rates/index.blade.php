@extends('layouts.app')
@section('title','Rates')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Rates</h1>
  <div>
    <x-help-button id="help-rates-index">
      <ul>
        <li>Gunakan filter Origin/Destination/Service/Active untuk menyaring data.</li>
        <li>Import CSV untuk masal, Export CSV untuk unduh semua rate.</li>
        <li>Hindari duplikasi kombinasi Origin+Destination+Service.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('rates.export') }}" class="btn btn-outline-secondary ml-2 mr-2"><i class="fas fa-file-export mr-1"></i> Export CSV</a>
    <a href="{{ route('rates.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add Rate</a>
  </div>
</div>

<form method="get" class="mb-3">
  <div class="form-row align-items-end">
    <div class="col-md-3">
      <label>Origin</label>
      <select name="origin_id" class="form-control select2">
        <option value="">All</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}" {{ (int)($originId ?? 0) === $loc->id ? 'selected' : '' }}>
            {{ $loc->city }}{{ $loc->province ? ', '.$loc->province : '' }}{{ $loc->country ? ' - '.$loc->country : '' }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label>Destination</label>
      <select name="destination_id" class="form-control select2">
        <option value="">All</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}" {{ (int)($destinationId ?? 0) === $loc->id ? 'selected' : '' }}>
            {{ $loc->city }}{{ $loc->province ? ', '.$loc->province : '' }}{{ $loc->country ? ' - '.$loc->country : '' }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label>Service</label>
      <input type="text" name="service_type" value="{{ $service }}" class="form-control" placeholder="e.g. REG, EXP">
    </div>
    <div class="col-md-2">
      <label>Active</label>
      <select name="active" class="form-control select2">
        <option value="">All</option>
        <option value="1" {{ (string)$active === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ (string)$active === '0' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">Filter</button>
    </div>
  </div>
</form>

@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">All Rates</h6>
    <form action="{{ route('rates.import') }}" method="post" enctype="multipart/form-data" class="form-inline">
      @csrf
      <div class="form-group mr-2">
        <input type="file" name="file" class="form-control-file" accept=".csv" required>
      </div>
      <button type="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-file-import mr-1"></i> Import</button>
    </form>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Origin</th>
            <th>Destination</th>
            <th>Service</th>
            <th>Price</th>
            <th>Lead Time</th>
            <th>Min Kg</th>
            <th>Max Kg</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rates as $r)
            <tr>
              <td>{{ $r->origin?->city }}</td>
              <td>{{ $r->destination?->city }}</td>
              <td>{{ $r->service_type }}</td>
              <td>{{ number_format((float)$r->price,2) }}</td>
              <td>{{ $r->lead_time }}</td>
              <td>{{ $r->min_weight ? (int)$r->min_weight.' kg' : '—' }}</td>
              <td>{{ $r->max_weight ? (int)$r->max_weight.' kg' : '—' }}</td>
              <td>
                @if($r->is_active)
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-secondary">Inactive</span>
                @endif
              </td>
              <td>
                <a href="{{ route('rates.edit',$r) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="{{ route('rates.destroy',$r) }}" method="POST" class="d-inline" data-confirm="Hapus rate ini?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $rates->links() }}
  </div>
</div>
@endsection
