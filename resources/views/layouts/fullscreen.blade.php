<!DOCTYPE html>
<html lang="en">
<head>
    <title>Packing Slip - Kovai Transport</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jquery.mCustomScrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2-theme.css') }}?v={{ filemtime(public_path('assets/css/select2-theme.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/app-custom.css') }}">
    @stack('styles')
</head>
<body style="background:#f4f6fb;">
    @yield('content')
    @include('partials.footer')
    @stack('scripts')
</body>
</html>