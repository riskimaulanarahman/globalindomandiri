@php($isEdit = $user && $user->exists)

<form method="POST" action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}">
  @csrf
  @if($isEdit)
    @method('PUT')
  @endif

  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label for="password">{{ $isEdit ? 'Password (optional)' : 'Password' }}</label>
    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $isEdit ? '' : 'required' }}>
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label for="password_confirmation">Confirm Password</label>
    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>

