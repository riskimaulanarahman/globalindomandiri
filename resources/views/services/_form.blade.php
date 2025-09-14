<form method="POST" action="{{ $service->exists ? route('services.update',$service) : route('services.store') }}">
  @csrf
  @if($service->exists) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$service->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="code">Code</label>
      <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code',$service->code) }}" required>
      @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="is_active">Active</label>
      <select id="is_active" name="is_active" class="form-control">
        <option value="1" {{ old('is_active',$service->is_active) ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ !old('is_active',$service->is_active) ? 'selected' : '' }}>No</option>
      </select>
    </div>
  </div>


  <div>
    <button class="btn btn-primary" type="submit">Save</button>
    <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>

