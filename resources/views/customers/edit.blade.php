@extends('layouts.app')
@section('title','Edit Customer')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit Customer</h1>
  <div>
    <x-help-button id="help-customers-edit" title="Bantuan • Edit Customer">
      <ul>
        <li><strong>Code</strong>: kode unik (tidak boleh duplikat). Contoh: CUST-001.</li>
        <li><strong>Name</strong>: nama perusahaan/pelanggan. Contoh: PT DARMA HENWA.</li>
        <li><strong>PIC</strong> & <strong>Phone</strong>: kontak/telepon penanggung jawab.</li>
        <li><strong>Email</strong>: email aktif (unik). Contoh: finance@contoh.co.id.</li>
        <li><strong>Payment Term</strong>: TOP dalam hari. Contoh: 30.</li>
        <li><strong>Credit Limit</strong> & <strong>Notes</strong>: opsional untuk pengingat internal.</li>
        <li>Klik <em>Save</em> untuk menyimpan perubahan.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary ml-2">Back</a>
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">Customer {{ session('status') }}.</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('customers._form')
</div></div>
@endsection
