<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (!empty($generalSettings['workspace_favicon']) && file_exists(public_path($generalSettings['workspace_favicon'])))
        <link rel="icon" href="{{ asset($generalSettings['workspace_favicon']) }}" type="image/png">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @endif

    <title>
        @hasSection('title')
            @yield('title') · {{ $generalSettings['workspace_name'] ?? 'Lead CRM' }}
        @else
            {{ $generalSettings['workspace_name'] ?? 'Lead CRM' }}
        @endif
    </title>

    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('css')

</head>

<body class="@yield('body-class', 'login-page')">

    @yield('content')

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor/axios.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2.all.min.js') }}"></script>

    <!-- Common JS -->
    <script src="{{ asset('assets/js/common/dom.js') }}"></script>
    <script src="{{ asset('assets/js/common/axios.js') }}"></script>
    <script src="{{ asset('assets/js/common/loader.js') }}"></script>
    <script src="{{ asset('assets/js/common/notification.js') }}"></script>
    <script src="{{ asset('assets/js/common/validation.js') }}"></script>
    <script src="{{ asset('assets/js/common/response.js') }}"></script>
    
    {{-- Session Notification  --}}
    <x-notifications />

    @stack('js')

</body>

</html>
