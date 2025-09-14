<form method="POST" action="{{ $tnc->exists ? route('terms-conditions.update',$tnc) : route('terms-conditions.store') }}">
  @csrf
  @if($tnc->exists) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="services">Services</label>
      <select id="services" name="services[]" class="form-control select2 @error('services') is-invalid @enderror" multiple required data-placeholder="-- Select Services --">
        @php($selected = collect(old('services', isset($tnc) ? $tnc->services?->pluck('id')->all() : []))->map(fn($v)=> (int)$v)->all())
        @foreach($services as $svc)
          <option value="{{ $svc->id }}" {{ in_array($svc->id, $selected, true) ? 'selected' : '' }}>{{ $svc->name }}</option>
        @endforeach
      </select>
      @error('services')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title',$tnc->title) }}" required>
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="version">Version</label>
      <input type="text" id="version" name="version" class="form-control @error('version') is-invalid @enderror" value="{{ old('version',$tnc->version) }}" placeholder="v1">
      @error('version')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="effective_from">Effective From</label>
      <input type="date" id="effective_from" name="effective_from" class="form-control @error('effective_from') is-invalid @enderror" value="{{ old('effective_from',$tnc->effective_from?->format('Y-m-d')) }}">
      @error('effective_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="effective_to">Effective To</label>
      <input type="date" id="effective_to" name="effective_to" class="form-control @error('effective_to') is-invalid @enderror" value="{{ old('effective_to',$tnc->effective_to?->format('Y-m-d')) }}">
      @error('effective_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="is_active">Active</label>
      <select id="is_active" name="is_active" class="form-control">
        <option value="1" {{ old('is_active',$tnc->is_active) ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ !old('is_active',$tnc->is_active) ? 'selected' : '' }}>No</option>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label for="body">Body</label>
    <textarea id="body" name="body" rows="8" class="form-control @error('body') is-invalid @enderror" placeholder="Enter terms & conditions text">{{ old('body',$tnc->body) }}</textarea>
    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div>
    <button class="btn btn-primary" type="submit">Save</button>
    <a href="{{ route('terms-conditions.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>

