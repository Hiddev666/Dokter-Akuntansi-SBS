<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen" x-data>
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Mobile Top Bar -->
            <div class="md:hidden fixed top-0 left-0 right-0 z-40 h-16 bg-white border-b border-gray-200 flex items-center px-4">
                <button
                    @click="$dispatch('toggle-sidebar')"
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 ml-3">
                    <x-application-logo class="h-8 w-8 text-indigo-600" />
                    <span class="font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
                </a>
            </div>

            <!-- Main Content -->
            <div
                class="transition-all duration-300 md:pl-64"
                :class="$store.sidebar.collapsed ? 'md:!pl-20' : 'md:!pl-64'"
            >

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8 md:mt-0 mt-16">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
