<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QR-Code System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div x-data="{ open: false }" class="min-h-screen">
            @include('layouts.sidebar')

            {{-- Konten utama (offset sidebar pada layar besar) --}}
            <div class="lg:pl-64 flex flex-col min-h-screen">
                {{-- Topbar --}}
                <header class="sticky top-0 z-30 h-16 bg-white/90 backdrop-blur border-b border-gray-100 flex items-center gap-4 px-4 sm:px-6 lg:px-8">
                    <button @click="open = true" class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="flex-1 min-w-0">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                    <div class="hidden sm:flex items-center gap-2 shrink-0">
                        @isset($actions)
                            {{ $actions }}
                        @endisset
                    </div>
                </header>

                {{-- Aksi mobile (tampil di bawah judul saat layar kecil) --}}
                @isset($actions)
                    <div class="sm:hidden px-4 pt-4">
                        {{ $actions }}
                    </div>
                @endisset

                @if (session('status'))
                    <div class="px-4 sm:px-6 lg:px-8 pt-4">
                        <x-alert>{{ session('status') }}</x-alert>
                    </div>
                @endif

                <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                    {{ $slot }}
                </main>

                <footer class="py-5 text-center text-xs text-gray-400">
                    {{ config('app.name', 'QR-Code System') }} &middot; Sistem Absensi QR
                </footer>
            </div>
        </div>
    </body>
</html>
