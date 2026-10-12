<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-5">
            @if (! $student)
                <div class="card">
                    <div class="card-body">
                        <p class="text-sm text-red-600">
                            Akun Anda belum terhubung ke data siswa. Hubungi admin.
                        </p>
                    </div>
                </div>
            @else
                {{-- Sapaan + tombol scan besar (kiosk-style) --}}
                <div class="card overflow-hidden">
                    <div class="bg-gradient-to-br from-brand-600 to-brand-700 p-6 text-white">
                        <p class="text-sm text-brand-100">Halo,</p>
                        <p class="text-2xl font-bold">{{ $student->nama }}</p>
                        <p class="text-sm text-brand-100 mt-0.5">NIS {{ $student->nis }}</p>
                    </div>
                    <div class="p-6">
                        <a href="{{ route('scan.index') }}"
                           class="flex items-center justify-center gap-3 w-full px-6 py-5 bg-brand-600 text-white rounded-xl font-bold text-lg shadow-sm hover:bg-brand-700 active:scale-[0.99] transition">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M15 3h4a2 2 0 012 2v4M21 15v4a2 2 0 01-2 2h-4M9 21H5a2 2 0 01-2-2v-4M3 12h18"/>
                            </svg>
                            Scan QR Absensi
                        </a>
                    </div>
                </div>

                {{-- Rekap singkat --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach (['hadir' => ['Hadir', 'badge-hadir'], 'telat' => ['Telat', 'badge-telat'], 'izin' => ['Izin', 'badge-izin'], 'sakit' => ['Sakit', 'badge-sakit']] as $key => [$label, $badge])
                        <div class="card p-4 text-center">
                            <p class="text-3xl font-bold text-gray-900">{{ $summary[$key] }}</p>
                            <p class="mt-1"><span class="{{ $badge }}">{{ $label }}</span></p>
                        </div>
                    @endforeach
                </div>

                {{-- Sesi hari ini --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="font-semibold text-gray-900 mb-3">Sesi Kelas Hari Ini</h3>
                        @if ($sessionsToday->isEmpty())
                            <p class="text-sm text-gray-500">Tidak ada sesi kelas untuk Anda hari ini.</p>
                        @else
                            <ul class="divide-y divide-gray-100 text-sm">
                                @foreach ($sessionsToday as $session)
                                    <li class="py-3 flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <span class="inline-flex h-2 w-2 rounded-full bg-brand-500"></span>
                                            <span class="font-medium text-gray-900">{{ $session->classRoom?->nama }}</span>
                                        </span>
                                        <span class="text-gray-500">
                                            {{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
