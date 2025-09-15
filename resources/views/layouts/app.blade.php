<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.6.1/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <style>
        /* Select2: keep borders styled like Bootstrap form controls */
        .select2-container--bootstrap4 .select2-selection--single,
        .select2-container--bootstrap4 .select2-selection--multiple {
            border: 1px solid #ced4da !important;
            background-color: #fff;
            border-radius: .35rem;
            min-height: calc(1.5em + .75rem + 2px);
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            padding-left: .5rem;
            padding-right: 2rem; /* space for arrow */
            line-height: calc(1.5em + .75rem + 2px);
            color: #495057;
        }
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #6c757d;
        }

        .select2-container--bootstrap4 .select2-selection--single:focus,
        .select2-container--bootstrap4 .select2-selection--multiple:focus,
        .select2-container--bootstrap4.select2-container--focus .select2-selection--single,
        .select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
            border-color: #80bdff !important;
            box-shadow: 0 0 0 .2rem rgba(0,123,255,.25) !important;
        }

        /* Ensure arrow aligns nicely */
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: .5rem;
        }

        /* Multiple selection paddings and chip style */
        .select2-container--bootstrap4 .select2-selection--multiple {
            padding: .375rem .5rem;
        }
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            border: 1px solid #ced4da;
            background-color: #e9ecef;
            color: #495057;
        }

        /* Dropdown search field border */
        .select2-dropdown .select2-search__field {
            border: 1px solid #ced4da !important;
            border-radius: .25rem;
            padding: .375rem .5rem;
            outline: none !important;
        }
        /* Layout polish for filters, tables, responsiveness */
        .card .card-header h6 { letter-spacing: .2px; }
        /* Add spacing between controls in filter rows */
        form.mb-3 .form-row > [class^="col-"],
        form.mb-3 .form-row > .col,
        form .form-row > [class^="col-"],
        form .form-row > .col { margin-bottom: .5rem; }

        /* Make table headers sticky within responsive scroll */
        .table-sticky thead th { position: sticky; top: 0; z-index: 1; background: #f8f9fc; }
        .table thead th { white-space: nowrap; }
        .table td, .table th { vertical-align: middle; }

        /* Responsive helpers */
        @media (max-width: 576px) {
            .topbar .navbar-nav { width: 100%; }
            .topbar .navbar-nav .nav-item { width: 100%; }
            .card .card-header { padding: .5rem .75rem; }
            .card .card-body { padding: .75rem; }
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center py-3" href="/dashboard">
                <img src="{{ asset('img/rrgm-logo.png') }}" alt="RRGM" style="max-height: 80px; width: auto;" />
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item @if(request()->is('dashboard')) active @endif">
                <a class="nav-link" href="/dashboard"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
@php(
                $isMaster = request()->routeIs('customers.*') || request()->routeIs('locations.*') || request()->routeIs('services.*') || request()->routeIs('terms-conditions.*') || request()->routeIs('rates.*') || request()->routeIs('payment-terms.*')
            )
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="{{ $isMaster ? 'true' : 'false' }}" aria-controls="collapseMaster">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Master Data</span>
                </a>
                <div id="collapseMaster" class="collapse {{ $isMaster ? 'show' : '' }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">Customers</a>
                        <a class="collapse-item {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}">Locations</a>
                        <a class="collapse-item {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Services</a>
                        <a class="collapse-item {{ request()->routeIs('terms-conditions.*') ? 'active' : '' }}" href="{{ route('terms-conditions.index') }}">Terms & Conditions</a>
                        <a class="collapse-item {{ request()->routeIs('payment-terms.*') ? 'active' : '' }}" href="{{ route('payment-terms.index') }}">Payment Terms</a>
                        <a class="collapse-item {{ request()->routeIs('rates.*') ? 'active' : '' }}" href="{{ route('rates.index') }}">Rates</a>
                    </div>
                </div>
            </li>

            @php($isSales = request()->routeIs('quotations.*'))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSales" aria-expanded="{{ $isSales ? 'true' : 'false' }}" aria-controls="collapseSales">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Sales</span>
                </a>
                <div id="collapseSales" class="collapse {{ $isSales ? 'show' : '' }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('quotations.*') ? 'active' : '' }}" href="{{ route('quotations.index') }}">Quotations</a>
                    </div>
                </div>
            </li>

            @php($isOps = request()->routeIs('shipments.*'))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOps" aria-expanded="{{ $isOps ? 'true' : 'false' }}" aria-controls="collapseOps">
                    <i class="fas fa-fw fa-truck"></i>
                    <span>Operations</span>
                </a>
                <div id="collapseOps" class="collapse {{ $isOps ? 'show' : '' }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('shipments.*') ? 'active' : '' }}" href="{{ route('shipments.index') }}">Shipments</a>
                    </div>
                </div>
            </li>

            @php($isFin = request()->routeIs('invoices.*') || request()->routeIs('payments.*'))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFinance" aria-expanded="{{ $isFin ? 'true' : 'false' }}" aria-controls="collapseFinance">
                    <i class="fas fa-fw fa-coins"></i>
                    <span>Finance</span>
                </a>
                <div id="collapseFinance" class="collapse {{ $isFin ? 'show' : '' }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">Invoices</a>
                        <a class="collapse-item {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">Payments</a>
                    </div>
                </div>
            </li>

            @php($isRpt = request()->routeIs('ui.reports'))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" aria-expanded="{{ $isRpt ? 'true' : 'false' }}" aria-controls="collapseReports">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
                <div id="collapseReports" class="collapse {{ $isRpt ? 'show' : '' }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('ui.reports') ? 'active' : '' }}" href="{{ route('ui.reports') }}">Reports</a>
                    </div>
                </div>
            </li>

            @php($isAdmin = request()->routeIs('users.*'))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdmin" aria-expanded="{{ $isAdmin ? 'true' : 'false' }}" aria-controls="collapseAdmin">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Administration</span>
                </a>
                <div id="collapseAdmin" class="collapse {{ $isAdmin ? 'show' : '' }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a>
                    </div>
                </div>
            </li>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name ?? 'User' }}</span>
                                <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}" alt="User">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert confirm handler
        document.body.addEventListener('submit', function (e) {
            var form = e.target;
            var msg = form.getAttribute('data-confirm');
            if (msg) {
                e.preventDefault();
                Swal.fire({
                    title: 'Anda yakin?',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        }, true);

        // Initialize Select2
        if (window.jQuery && jQuery().select2) {
            jQuery('.select2').each(function(){
                var $el = jQuery(this);
                $el.select2({ theme: 'bootstrap4', width: '100%' });
            });
        }

        // Show validation errors via SweetAlert
        @if($errors->any())
        try {
            const messages = @json($errors->all());
            const html = '<ul style="text-align:left; margin:0; padding-left:18px;">' + messages.map(m => `<li>${m}</li>`).join('') + '</ul>';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: html,
            });
        } catch(e) { /* no-op */ }
        @endif

        // Optional: success toast
        @if(session('status'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false,
            icon: 'success',
            title: '{{ ucfirst(str_replace('-', ' ', session('status'))) }}'
        });
        @endif
    });
    </script>
    @stack('scripts')
 </body>
</html>
