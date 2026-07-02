<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ระบบบันทึกการเลี้ยงไก่เนื้อ') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900" x-data="{ sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true', sidebarOpen: false }">
        <div class="min-h-screen bg-slate-50">
            @include('layouts.navigation')

            <div class="transition-all duration-300 ease-in-out" :class="sidebarCollapsed ? 'lg:pl-0' : 'lg:pl-72'">
                <!-- Toggle Button for Desktop (Visible only when sidebar is collapsed) -->
                <div x-show="sidebarCollapsed" class="hidden lg:block fixed left-4 top-4 z-40">
                    <button @click="sidebarCollapsed = false; localStorage.setItem('sidebar-collapsed', 'false')" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 shadow-md hover:bg-slate-50 hover:text-slate-950 transition-all duration-200 active:scale-95" title="แสดงเมนู">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                @isset($header)
                    <header class="border-b border-slate-200 bg-white/90 backdrop-blur">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
