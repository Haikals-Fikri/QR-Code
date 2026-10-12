<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Laporan Kehadiran" subtitle="Rekap kehadiran per kelas." />
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.reports.manual.form') }}" class="btn-secondary">+ Absensi Manual</a>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <h3 class="font-semibold text-gray-900 mb-4">Pilih Kelas untuk Melihat Rekap</h3>
            @if ($classes->isEmpty())
                <p class="text-sm text-gray-500">Belum ada kelas.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($classes as $class)
                        <a href="{{ route('admin.reports.class', $class) }}"
                           class="block card p-5 hover:shadow-card-hover hover:border-brand-300 transition">
                            <p class="font-semibold text-gray-900">{{ $class->nama }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $class->kode }}</p>
                            <p class="text-sm text-gray-500 mt-3 flex items-center gap-3">
                                <span>{{ $class->students_count }} siswa</span>
                                <span class="text-gray-300">·</span>
                                <span>{{ $class->sessions_count }} sesi</span>
                            </p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
