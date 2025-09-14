@php $isEdit = $rate && $rate->exists; @endphp

<form method="POST" action="{{ $isEdit ? route('rates.update',$rate) : route('rates.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="origin_id">Origin</label>
      <select name="origin_id" id="origin_id" class="form-control select2 @error('origin_id') is-invalid @enderror" required>
        <option value="">-- Select Origin --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}" {{ (int)old('origin_id',$rate->origin_id) === $loc->id ? 'selected' : '' }}>
            {{ $loc->city }}{{ $loc->province ? ', '.$loc->province : '' }}{{ $loc->country ? ' - '.$loc->country : '' }}
          </option>
        @endforeach
      </select>
      @error('origin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="destination_id">Destination</label>
      <select name="destination_id" id="destination_id" class="form-control select2 @error('destination_id') is-invalid @enderror" required>
        <option value="">-- Select Destination --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}" {{ (int)old('destination_id',$rate->destination_id) === $loc->id ? 'selected' : '' }}>
            {{ $loc->city }}{{ $loc->province ? ', '.$loc->province : '' }}{{ $loc->country ? ' - '.$loc->country : '' }}
          </option>
        @endforeach
      </select>
      @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="service_type">Service Type</label>
      <select name="service_type" id="service_type" class="form-control select2 @error('service_type') is-invalid @enderror" required>
        <option value="">-- Select Service --</option>
        @foreach(($serviceOptions ?? []) as $opt)
          <option value="{{ $opt['value'] }}" {{ old('service_type', $rate->service_type) === $opt['value'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
        @endforeach
      </select>
      @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="price">Price</label>
      <input type="number" step="0.01" min="0" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price',$rate->price) }}" required>
      @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="lead_time">Lead Time</label>
      <input type="text" name="lead_time" id="lead_time" class="form-control @error('lead_time') is-invalid @enderror" value="{{ old('lead_time',$rate->lead_time) }}" placeholder="e.g. 2-3 days">
      @error('lead_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="min_weight">Min Kg</label>
      <input type="number" step="1" min="0" name="min_weight" id="min_weight" class="form-control @error('min_weight') is-invalid @enderror" value="{{ old('min_weight',$rate->min_weight) }}" placeholder="e.g. 10">
      @error('min_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="max_weight">Max Kg</label>
      <input type="number" step="1" min="0" name="max_weight" id="max_weight" class="form-control @error('max_weight') is-invalid @enderror" value="{{ old('max_weight',$rate->max_weight) }}" placeholder="e.g. 30">
      @error('max_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <div class="form-check" style="margin-top: 2rem;">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active',$rate->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
      </div>
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('rates.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>
