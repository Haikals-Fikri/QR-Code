<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('QR Absensi') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sessions.print', $session) }}" target="_blank" class="btn-secondary">
                    Cetak / Unduh
                </a>
                <a href="{{ route('admin.schedules.sessions', $session->schedule_id) }}" class="btn-secondary">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert class="mb-4">{{ session('status') }}</x-alert>
            @endif

            <div class="card">
                <div class="p-10 text-center">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $session->classRoom?->nama }}</h3>
                    <p class="text-gray-500 mt-1">
                        {{ $session->tanggal?->translatedFormat('l, d F Y') }} ·
                        {{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}
                    </p>

                    <div class="mt-8 inline-block p-5 bg-white border border-gray-200 rounded-2xl shadow-card">
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
