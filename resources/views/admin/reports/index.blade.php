<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Kehadiran') }}
            </h2>
            <a href="{{ route('admin.reports.manual.form') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                + Absensi Manual (Izin/Sakit)
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-medium mb-4">Pilih Kelas untuk Melihat Rekap</h3>
                    @if ($classes->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada kelas.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($classes as $class)
                                <a href="{{ route('admin.reports.class', $class) }}"
                                   class="block border border-gray-200 rounded-lg p-4 hover:border-indigo-400 hover:shadow-sm transition">
                                    <p class="font-medium text-gray-900">{{ $class->nama }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $class->kode }}</p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        {{ $class->students_count }} siswa · {{ $class->sessions_count }} sesi
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
