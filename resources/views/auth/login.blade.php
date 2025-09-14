@extends('layouts.guest-sb')
@section('title','Login')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10 col-md-11">
            <div class="card o-hidden border-0 shadow-lg my-5 auth-card">
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <div class="col-lg-5 left-pane">
                            <div class="auth-brand">
                                <img src="{{ asset('img/rrgm-logo.png') }}" alt="Logo">
                                <div class="name">{{ strtoupper(config('app.name')) }}</div>
                                <div class="tag">Fast • Reliable • Secure</div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <h1 class="h4 auth-title mb-1">Sign in</h1>
                                    <div class="auth-subtitle">Welcome back, please enter your details</div>
                                </div>
                                @if (session('status'))
                                    <div class="alert alert-success">{{ session('status') }}</div>
                                @endif
                                <form method="POST" action="{{ route('login') }}" class="user">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter Email Address...">
                                        @error('email')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-user" id="password" name="password" required placeholder="Password">
                                            <div class="input-group-append">
                                                <button class="btn btn-light" type="button" id="togglePwd" tabindex="-1"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        @error('password')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="form-group d-flex justify-content-between align-items-center">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                                            <label class="custom-control-label" for="remember_me">Remember Me</label>
                                        </div>
                                        <div>
                                            @if (Route::has('password.request'))
                                                <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">Log In</button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    @if (Route::has('register'))
                                        <a class="small" href="{{ route('register') }}">Create an Account!</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('togglePwd');
    var input = document.getElementById('password');
    if (btn && input) {
      btn.addEventListener('click', function(){
        var is = input.getAttribute('type') === 'password';
        input.setAttribute('type', is ? 'text' : 'password');
        this.innerHTML = is ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
      });
    }
  });
</script>
@endsection
