<!DOCTYPE html>
<html lang="en">

<head>
    <title>Transport Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CodedThemes">
    <meta name="keywords" content=" Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="CodedThemes">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jquery.mCustomScrollbar.css') }}">
    {{-- Select2 --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2/select2.min.css') }}">
    {{-- DataTables --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatable/dataTables.bootstrap4.min.css') }}">
    {{-- Toastr --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
    {{-- Select2 theme overrides --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2-theme.css') }}?v={{ filemtime(public_path('assets/css/select2-theme.css')) }}">
    {{-- App custom styles --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/app-custom.css') }}">

    @stack('styles')
    <style>
        /* ── Remove tick/checkmark on selected option in Select2 dropdown ── */
    .select2-results__option[aria-selected="true"]::before,
    .select2-results__option--selected::before {
        display: none !important;
    }
    .select2-results__option[aria-selected="true"] {
        background-color: #f0f4ff !important;
        color: #1a2340 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #667eea !important;
        color: #fff !important;
    }
    .select2-results__option--highlighted[aria-selected] span,
    .select2-results__option--highlighted[aria-selected] div {
        color: #fff !important;
    }
    </style>
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            {{-- header partial --}}
            @include('partials.header')

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">

                    {{-- sidebar partial --}}
                    @include('partials.sidebar')

                    <div class="pcoded-content">
                        @yield('content')
                    </div>

                </div>
            </div>

            {{-- footer (scripts + fixed button) --}}
            @include('partials.footer')

            {{-- Global Delete Confirmation Modal --}}
            @include('partials.delete-modal')

        </div>
    </div>

    {{-- Page-level scripts pushed via @push('scripts') --}}

    @stack('scripts')

    <script>
        $(document).ready(function() {
            if (typeof initSelect2Events === 'function') {
                initSelect2Events();
            }

            // Keep submenu open for active parent — pcoded uses pcoded-trigger
            $('.pcoded-hasmenu.pcoded-trigger').each(function() {
                $(this).find('> .pcoded-submenu').css('display', 'block');
            });
        });
    </script>

</body>

</html>