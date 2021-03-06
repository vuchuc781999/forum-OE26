<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    @include('includes.navbar')
    @include('includes.sidebar')
    <div class="row d-flex justify-content-end">
        <div class="content">
            @yield('content')
        </div>
    </div>
    @auth
        <input type="hidden" value="{{ Auth::id() }}" id="auth-id"/>
    @endauth
</body>
</html>
