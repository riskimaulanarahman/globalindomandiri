@extends('layouts.app')
@section('title','Terms & Conditions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Terms & Conditions</h1>
  <a href="{{ route('terms-conditions.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New</a>
</div>

<div class="card shadow mb-4">
  <div class="card-body">
    <form method="get" class="mb-3">
      <div class="form-row">
        <div class="form-group col-md-4">
          <label class="small mb-1">Search</label>
          <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Title / Version / Body">
        </div>
        <div class="form-group col-md-4">
          <label class="small mb-1">Service</label>
          <select name="service_id" class="form-control select2" data-placeholder="-- Service --">
            <option value="">-- Service --</option>
            @foreach($services as $svc)
              <option value="{{ $svc->id }}" {{ (int)($serviceId ?? 0) === $svc->id ? 'selected' : '' }}>{{ $svc->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-2">
          <label class="small mb-1">Active</label>
          <select name="active" class="form-control">
            <option value="">-- Any --</option>
            <option value="1" {{ ($active === '1') ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ ($active === '0') ? 'selected' : '' }}>No</option>
          </select>
        </div>
        <div class="form-group col-md-2 d-flex align-items-end">
          <button class="btn btn-outline-secondary btn-block" type="submit">Filter</button>
        </div>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-sticky">
        <thead>
          <tr>
            <th>Services</th>
            <th>Title</th>
            <th>Version</th>
            <th>Active</th>
            <th>Updated</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $it)
            <tr>
              <td>{{ ($it->services?->pluck('name')->implode(', ')) ?: '—' }}</td>
              <td>{{ $it->title }}</td>
              <td>{{ $it->version ?: '—' }}</td>
              <td>{!! $it->is_active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>' !!}</td>
              <td>{{ $it->updated_at?->format('Y-m-d H:i') }}</td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('terms-conditions.edit',$it) }}">Edit</a>
                <form action="{{ route('terms-conditions.destroy',$it) }}" method="POST" class="d-inline" data-confirm="Hapus T&C ini?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div>
      {{ $items->links() }}
    </div>
  </div>
</div>
@endsection

