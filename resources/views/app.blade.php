<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="site-language" content="{{ app()->getLocale() }}">
    <title>{{ config('app.name', 'Optima Exchange') }}</title>
    <!-- Styles -->
    <link rel="icon" type="image/png" href="{{ url('/favicon.png') }}" sizes="42x42">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/site.css') }}">
    <!-- Scripts -->
    @routes
    <script src="{{ url('/js/type.js') }}"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>

    @if(config('app.analytics'))
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('app.analytics') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', {{ config('app.analytics') }});
    </script>
    @endif
</head>
<body class="{{ $themeMode }}">
@inertia
</body>
</html>
