<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (! $student)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="text-sm text-red-600">
                            Akun Anda belum terhubung ke data siswa. Hubungi admin.
                        </p>
                    </div>
                </div>
            @else
                {{-- Sapaan + tombol scan --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <p class="font-medium text-gray-900">Halo, {{ $student->nama }}.</p>
                            <p class="text-sm text-gray-500">NIS {{ $student->nis }}</p>
                        </div>
                        <a href="{{ route('scan.index') }}"
                           class="inline-flex items-center px-5 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700">
                            📷 Scan QR Absensi
                        </a>
                    </div>
                </div>

                {{-- Rekap singkat --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach (['hadir' => 'Hadir', 'telat' => 'Telat', 'izin' => 'Izin', 'sakit' => 'Sakit'] as $key => $label)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $summary[$key] }}</p>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Sesi hari ini --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-medium text-gray-900 mb-3">Sesi Kelas Hari Ini</h3>
                        @if ($sessionsToday->isEmpty())
                            <p class="text-sm text-gray-500">Tidak ada sesi kelas untuk Anda hari ini.</p>
                        @else
                            <ul class="divide-y divide-gray-100 text-sm">
                                @foreach ($sessionsToday as $session)
                                    <li class="py-2 flex items-center justify-between">
                                        <span>
                                            <span class="font-medium">{{ $session->classRoom?->nama }}</span>
                                            <span class="text-gray-500">
                                                · {{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}
                                            </span>
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
