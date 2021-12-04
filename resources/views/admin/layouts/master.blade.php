<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ site_info('name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/apps-admin.css?ver=1.0.0') }}">
@if(sys_settings('ui_theme_skin_admin', 'default') != 'default')
    <link rel="stylesheet" href="{{ asset('assets/css/skins/theme-'.sys_settings('ui_theme_skin_admin').'.css?ver=1.0.0') }}">
@endif
</head>

<body class="nk-body npc-cryptlite npc-admin has-sidebar">
<div class="nk-app-root">

    <div class="nk-main ">

        @include('admin.layouts.sidebar')

        <div class="nk-wrap @yield('has-content-sidebar')">

            @include('admin.layouts.header')

            @yield('content-sidebar')

            <div class="nk-content ">
                <div class="container-fluid ">

                    @include('misc.message-admin')
                    @include('misc.notices')

                    @yield('content')

                </div>
            </div>

            @include('admin.layouts.footer')

        </div>
    </div>
</div>

@stack('modal')

<script src="{{ asset('assets/js/bundle.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/app.admin.js') }}"></script>
@stack('scripts')

</body>
</html>
