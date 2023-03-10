<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" /> --}}

        <title inertia>{{ config('app.name', 'Sisgesc') }}</title>
        <!-- Fonts -->
        <link rel="stylesheet"  href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="shortcut icon" href="/login/image/logo.png" type="image/x-icon">
       <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
