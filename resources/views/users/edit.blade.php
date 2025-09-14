@extends('layouts.app')
@section('title','Edit User')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit User</h1>
  <div>
    <x-help-button id="help-users-edit" title="Bantuan • Edit User">
      <ul>
        <li><strong>Name</strong> & <strong>Email</strong>: perbarui bila diperlukan. Email harus unik.</li>
        <li><strong>Password</strong>: kosongkan jika tidak mengubah password.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary ml-2">Back</a>
  </div>
</div>

<div class="card shadow mb-4"><div class="card-body">
  @if (session('status') === 'created')
    <div class="alert alert-success">User created.</div>
  @elseif (session('status') === 'updated')
    <div class="alert alert-success">User updated.</div>
  @endif
  @include('users._form')
</div></div>
@endsection

