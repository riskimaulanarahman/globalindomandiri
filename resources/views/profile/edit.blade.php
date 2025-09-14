@extends('layouts.app')
@section('title','Profile')
@section('content')
<h1 class="h3 mb-3 text-gray-800">Profile</h1>

<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Profile Information</h6></div>
      <div class="card-body">
        @include('profile.partials.update-profile-information-form')
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Update Password</h6></div>
      <div class="card-body">
        @include('profile.partials.update-password-form')
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-danger">Delete Account</h6></div>
      <div class="card-body">
        @include('profile.partials.delete-user-form')
      </div>
    </div>
  </div>
</div>
@endsection
