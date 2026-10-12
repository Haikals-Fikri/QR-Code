<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('QR Absensi') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sessions.print', $session) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    Cetak / Unduh
                </a>
                <a href="{{ route('admin.schedules.sessions', $session->schedule_id) }}"
                   class="text-sm text-gray-600 hover:text-gray-900">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $session->classRoom?->nama }}</h3>
                    <p class="text-gray-500 mt-1">
                        {{ $session->tanggal?->translatedFormat('l, d F Y') }} ·
                        {{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}
                    </p>

                    <div class="mt-8 inline-block p-4 bg-white border border-gray-200 rounded-lg">
                        {!! $qrSvg !!}
                    </div>

                    <p class="mt-6 text-sm text-gray-600">
                        Pindai QR ini dengan aplikasi absensi untuk mencatat kehadiran.
                    </p>
                    <p class="mt-1 text-xs text-gray-400 font-mono">
                        Token: {{ substr($session->qr_token, 0, 16) }}…
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
