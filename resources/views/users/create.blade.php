@extends('layouts.app')
@section('title','Create User')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create User</h1>
  <x-help-button id="help-users-create" title="Bantuan • Create User">
    <ul>
      <li><strong>Name</strong>: nama lengkap user. Contoh: Budi Santoso.</li>
      <li><strong>Email</strong>: unik & aktif. Contoh: budi@contoh.co.id.</li>
      <li><strong>Password</strong> & <strong>Confirm</strong>: minimal 8 karakter dan sama.</li>
    </ul>
  </x-help-button>
</div>
<div class="card shadow mb-4"><div class="card-body">
  @include('users._form')
</div></div>
@endsection
