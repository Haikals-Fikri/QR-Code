<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Laporan Kehadiran') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Rekap kehadiran per kelas.</p>
            </div>
            <a href="{{ route('admin.reports.manual.form') }}" class="btn-secondary">
                + Absensi Manual (Izin/Sakit)
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <x-alert>{{ session('status') }}</x-alert>
            @endif

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
        </div>
    </div>
</x-app-layout>
