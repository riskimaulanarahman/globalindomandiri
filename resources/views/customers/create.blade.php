@extends('layouts.app')
@section('title','Create Customer')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800 mb-0">Create Customer</h1>
  <x-help-button id="help-customers-create" title="Bantuan • Create Customer">
    <ul>
      <li><strong>Code</strong>: kode unik customer. Contoh: CUST-001, PT-RRG-01.</li>
      <li><strong>Name</strong>: nama perusahaan/pelanggan. Contoh: PT DARMA HENWA.</li>
      <li><strong>PIC</strong>: penanggung jawab/kontak utama. Contoh: Ahmad Barokah.</li>
      <li><strong>Phone</strong>: nomor telepon. Contoh: +62 812-1234-5678.</li>
      <li><strong>Email</strong>: email aktif (unik). Contoh: finance@contoh.co.id.</li>
      <li><strong>NPWP</strong>: (opsional) nomor NPWP perusahaan.</li>
      <li><strong>Payment Term (days)</strong>: TOP. Contoh: 14 atau 30.</li>
      <li><strong>Credit Limit</strong>: (opsional) batas piutang. Contoh: 10000000.</li>
      <li><strong>Notes</strong>: catatan internal. Contoh: “Tagih tiap akhir bulan”.</li>
      <li>Tekan <em>Save</em> untuk menyimpan, semua kolom bertanda wajib harus diisi.</li>
    </ul>
  </x-help-button>
  </div>
<div class="card shadow mb-4"><div class="card-body">
  @include('customers._form')
</div></div>
@endsection
