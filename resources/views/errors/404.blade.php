<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="site-language" content="{{ app()->getLocale() }}">
    <title>{{ config('app.name', 'Optima Exchange') }}</title>
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/site.css') }}">
    <!-- Scripts -->
    @routes
</head>
<body>
    <div class="site-layout">
        <section class="text-gray-600 body-font">
            <div class="container px-5 py-24 mx-auto">
                <div class="flex flex-col text-center w-full mb-20">
                    <h1 class="text-2xl font-medium title-font mb-4 text-gray-900 md:text-9xl sm:text-9xl">404</h1>
                    <p class="lg:w-2/3 mx-auto leading-relaxed text-base mb-10">Sorry! The page you’re looking for cannot be found.</p>
                    <p><a href="/" class="text-indigo-500">Go to Homepage</a></p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>


