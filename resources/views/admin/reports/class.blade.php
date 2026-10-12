<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Rekap Kehadiran') }} — {{ $class->nama }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Total {{ $totalSesi }} sesi · {{ $class->students->count() }} siswa</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.class.excel', $class) }}" class="btn-success">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/></svg>
                    Excel
                </a>
                <a href="{{ route('admin.reports.class.pdf', $class) }}" class="btn-danger">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/></svg>
                    PDF
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    @if ($students->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada siswa di kelas ini.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th>NIS</th>
                                        <th>Nama</th>
                                        <th class="text-center">Hadir</th>
                                        <th class="text-center">Telat</th>
                                        <th class="text-center">Izin</th>
                                        <th class="text-center">Sakit</th>
                                        <th class="text-center">Alpha</th>
                                        <th class="text-center">% Hadir</th>
                                        <th class="text-right">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $row)
                                        <tr>
                                            <td class="font-mono text-gray-600">{{ $row['nis'] }}</td>
                                            <td class="font-medium text-gray-900">{{ $row['nama'] }}</td>
                                            <td class="text-center text-emerald-700 font-medium">{{ $row['hadir'] }}</td>
                                            <td class="text-center text-amber-600 font-medium">{{ $row['telat'] }}</td>
                                            <td class="text-center text-blue-600 font-medium">{{ $row['izin'] }}</td>
                                            <td class="text-center text-purple-600 font-medium">{{ $row['sakit'] }}</td>
                                            <td class="text-center text-red-600 font-medium">{{ $row['alpha'] }}</td>
                                            <td class="text-center">
                                                <span class="font-semibold text-gray-900">{{ $row['persen_hadir'] }}%</span>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.reports.student', $row['student_id']) }}"
                                                   class="text-brand-600 hover:text-brand-800 font-medium">Lihat</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
