@php($isEdit = $location && $location->exists)

<form method="POST" action="{{ $isEdit ? route('locations.update',$location) : route('locations.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="city">City</label>
      <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city',$location->city) }}" required>
      @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="province">Province</label>
      <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province',$location->province) }}">
      @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="country">Country</label>
      <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country',$location->country) }}" required>
      @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>

