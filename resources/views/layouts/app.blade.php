<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            @if(session('status'))
                <div class="bg-emerald-50 border-b border-emerald-200/80 shadow-sm">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-4">
                        <p class="text-base font-medium text-emerald-800">{{ session('status') }}</p>
                        <div class="flex gap-3">
                            <a href="{{ url('/cart') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-700 text-sm font-semibold rounded-lg shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition !text-white">
                                {{ __('View cart') }}
                            </a>
                            <a href="{{ url('/checkout') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-900 text-sm font-semibold rounded-lg shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition !text-white">
                                {{ __('Checkout') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200/80 shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 py-8 sm:py-10">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
