<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'OJT360 - Internship Monitoring') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ojt-dark antialiased bg-gradient-to-br from-ojt-light via-white to-gray-50">
        <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8">
            <!-- Logo Section -->
            <div class="mb-8 text-center">
                <a href="/" class="inline-block">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-xl shadow-lg flex items-center justify-center mb-4">
                        <span class="text-white font-bold text-xl sm:text-2xl">OJT</span>
                    </div>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-primary mb-2">OJT360</h1>
                <p class="text-sm sm:text-base text-gray-600">Internship Monitoring System</p>
            </div>

            <!-- Auth Card -->
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-8 sm:px-8 sm:py-10">
                        {{ $slot }}
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} OJT360. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
