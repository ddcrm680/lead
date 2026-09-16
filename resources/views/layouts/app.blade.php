<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="description" content="Modern Lead CRM Dashboard">
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/tom-select.bootstrap5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/extra.css') }}">

    @stack('css')

</head>

<body>

    <a class="skip-link" href="#mainContent">
        Skip to content
    </a>

    @include('partials.sidebar')

    <div class="nav-backdrop" id="navBackdrop"></div>

    <main class="app-main" id="mainContent">

        @include('partials.header')

        <div class="content">
            @yield('content')
        </div>

        @include('partials.footer')

    </main>
    {{-- Global  canvas drawer --}}
    <x-global-drawer />
    
    {{-- mobile nav --}}
    <x-mobile-nav />

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor/axios.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jszip.min.js') }}"></script>

     <!-- Common JS -->
    <script src="{{ asset('assets/js/common/dom.js') }}"></script>
    <script src="{{ asset('assets/js/common/axios.js') }}"></script>
    <script src="{{ asset('assets/js/common/loader.js') }}"></script>
    <script src="{{ asset('assets/js/common/notification.js') }}"></script>
    <script src="{{ asset('assets/js/common/validation.js') }}"></script>
    <script src="{{ asset('assets/js/common/response.js') }}"></script>

    {{-- Session Notification  --}}
    <x-notifications />

    <!-- Application JS -->
    <script src="{{ asset('assets/js/xlsx-lite.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- Module JS -->
    <script src="{{ asset('assets/js/modules/account.js') }}"></script>
    <script src="{{ asset('assets/js/modules/lead.js')}}"></script>


    <script src="{{ asset('assets/js/mobile-nav.js') }}"></script>

    @stack('js')

</body>


</html>
