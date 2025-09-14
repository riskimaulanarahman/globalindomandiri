<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@yield('title', config('app.name'))</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        body.bg-gradient-primary {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #111827 0%, #1f2937 35%, #0ea5e9 100%);
        }
        .auth-card { border-radius: 16px; overflow: hidden; }
        .auth-card .left-pane {
            background: linear-gradient(180deg, rgba(14,165,233,.15), rgba(14,165,233,.05));
            display:flex; align-items:center; justify-content:center; padding: 24px;
        }
        .auth-brand { text-align:center; }
        .auth-brand img { height: 64px; width:auto; margin-bottom: 10px; }
        .auth-brand .name { font-weight:800; letter-spacing:.3px; color:#111827; }
        .auth-brand .tag { color:#6b7280; font-size: 12px; }
        .auth-title { font-weight: 800; color:#111827; }
        .auth-subtitle { color:#6b7280; }
        .form-control-user { border-radius: .5rem; }
        .btn-user { border-radius: .5rem; }
        @media (max-width: 992px) {
            .auth-card .left-pane { display:none; }
        }
    </style>
</head>
<body class="bg-gradient-primary">
    @yield('content')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>
</html>
