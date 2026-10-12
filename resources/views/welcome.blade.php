<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'QR-Code System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen flex flex-col bg-gradient-to-b from-brand-50 to-gray-50">
            <header class="px-6 py-5 max-w-6xl mx-auto w-full flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-application-logo class="h-9 w-9" />
                    <span class="font-bold text-lg tracking-tight">QR-Code System</span>
                </div>
                @auth
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn-primary">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">Masuk</a>
                @endauth
            </header>

            <main class="flex-1 flex items-center">
                <div class="max-w-6xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-block bg-brand-100 text-brand-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                            Sistem Absensi Modern
                        </span>
                        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight tracking-tight">
                            Absensi Kelas dengan <span class="text-brand-600">Scan QR</span>
                        </h1>
                        <p class="mt-5 text-lg text-gray-600 leading-relaxed">
                            Siswa cukup memindai QR kelas dari ponsel masing-masing.
                            Hadir, telat, izin, dan sakit tercatat otomatis beserta rekap dan laporannya.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn-primary py-3 px-6 text-base">
                                    Buka Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary py-3 px-6 text-base">
                                    Masuk ke Sistem
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="hidden lg:block">
                        <div class="card p-8">
                            <div class="mx-auto w-48 h-48 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center">
                                <svg class="h-28 w-28 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM15 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0115 9.375v-4.5zM3.75 15c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 19.5V15zM15 15c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0115 19.5V15z"/>
                                </svg>
                            </div>
                            <div class="mt-8 space-y-3">
                                @foreach (['Scan QR kelas dari ponsel', 'Status hadir & telat otomatis', 'Rekap + export Excel/PDF'] as $feature)
                                    <div class="flex items-center gap-3 text-sm text-gray-700">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-xs">✓</span>
                                        {{ $feature }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-6 text-center text-xs text-gray-400">
                {{ config('app.name', 'QR-Code System') }} &middot; Sistem Absensi QR
            </footer>
        </div>
    </body>
</html>
