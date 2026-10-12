<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rekap Kehadiran') }} — {{ $class->nama }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Total {{ $totalSesi }} sesi · {{ $class->students->count() }} siswa</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.class.excel', $class) }}"
                   class="inline-flex items-center px-3 py-2 bg-green-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    ⬇ Excel
                </a>
                <a href="{{ route('admin.reports.class.pdf', $class) }}"
                   class="inline-flex items-center px-3 py-2 bg-red-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    ⬇ PDF
                </a>
                <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($students->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada siswa di kelas ini.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="px-3 py-3 font-medium">NIS</th>
                                        <th class="px-3 py-3 font-medium">Nama</th>
                                        <th class="px-3 py-3 font-medium text-center">Hadir</th>
                                        <th class="px-3 py-3 font-medium text-center">Telat</th>
                                        <th class="px-3 py-3 font-medium text-center">Izin</th>
                                        <th class="px-3 py-3 font-medium text-center">Sakit</th>
                                        <th class="px-3 py-3 font-medium text-center">Alpha</th>
                                        <th class="px-3 py-3 font-medium text-center">% Hadir</th>
                                        <th class="px-3 py-3 font-medium text-right">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($students as $row)
                                        <tr>
                                            <td class="px-3 py-3 font-mono text-gray-700">{{ $row['nis'] }}</td>
                                            <td class="px-3 py-3">{{ $row['nama'] }}</td>
                                            <td class="px-3 py-3 text-center text-green-700">{{ $row['hadir'] }}</td>
                                            <td class="px-3 py-3 text-center text-amber-600">{{ $row['telat'] }}</td>
                                            <td class="px-3 py-3 text-center text-blue-600">{{ $row['izin'] }}</td>
                                            <td class="px-3 py-3 text-center text-purple-600">{{ $row['sakit'] }}</td>
                                            <td class="px-3 py-3 text-center text-red-600">{{ $row['alpha'] }}</td>
                                            <td class="px-3 py-3 text-center font-medium">{{ $row['persen_hadir'] }}%</td>
                                            <td class="px-3 py-3 text-right">
                                                <a href="{{ route('admin.reports.student', $row['student_id']) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">Lihat</a>
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
