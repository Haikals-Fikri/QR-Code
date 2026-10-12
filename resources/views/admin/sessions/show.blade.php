<x-app-layout>
    <x-slot name="header">
        <x-page-header title="QR Absensi" subtitle="Tampilkan QR ini di kelas (proyektor/layar)." />
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.schedules.sessions', $session->schedule_id) }}" class="btn-secondary">Kembali</a>
        <a href="{{ route('admin.sessions.print', $session) }}" target="_blank" class="btn-primary">Cetak / Unduh</a>
    </x-slot>

    <div class="max-w-3xl">
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
</x-app-layout>
