@extends('layouts.app')
@section('title','Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Users</h1>
  <div>
    <x-help-button id="help-users-index">
      <ul>
        <li>Daftar semua pengguna dengan aksi Edit/Delete.</li>
        <li>Klik Add User untuk menambah pengguna baru.</li>
        <li>Tidak bisa menghapus akun diri sendiri.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('users.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus mr-1"></i> Add User</a>
  </div>
  </div>

<x-ui.table title="All Users" :headers="['Name','Email','Actions']">
  @foreach($users as $u)
    <tr>
      <td>{{ $u->name }}</td>
      <td>{{ $u->email }}</td>
      <td>
        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        @if(auth()->id() !== $u->id)
        <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" data-confirm="Hapus user ini?">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
        @endif
      </td>
    </tr>
  @endforeach
</x-ui.table>

<div>
  {{ $users->links() }}
  </div>
@endsection
