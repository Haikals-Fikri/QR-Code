<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rekap Siswa') }} — {{ $student->nama }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">NIS {{ $student->nis }}</p>
            </div>
            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Ringkasan --}}
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                @foreach (['total_sesi' => 'Sesi', 'hadir' => 'Hadir', 'telat' => 'Telat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $key => $label)
                    <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $summary[$key] }}</p>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                <span class="text-sm text-gray-500">Persentase Kehadiran (Hadir + Telat):</span>
                <span class="text-lg font-bold text-indigo-600 ml-2">{{ $summary['persen_hadir'] }}%</span>
            </div>

            {{-- Riwayat --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-medium text-gray-900 mb-3">Riwayat Absensi</h3>
                    @if ($attendances->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada catatan absensi.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="px-3 py-2 font-medium">Tanggal</th>
                                        <th class="px-3 py-2 font-medium">Kelas</th>
                                        <th class="px-3 py-2 font-medium">Waktu Scan</th>
                                        <th class="px-3 py-2 font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($attendances as $attendance)
                                        <tr>
                                            <td class="px-3 py-2">{{ $attendance->session?->tanggal?->format('d M Y') }}</td>
                                            <td class="px-3 py-2">{{ $attendance->session?->classRoom?->nama }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $attendance->waktu_scan?->format('H:i:s') }}</td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $colors = ['hadir' => 'bg-green-100 text-green-800', 'telat' => 'bg-amber-100 text-amber-800', 'izin' => 'bg-blue-100 text-blue-800', 'sakit' => 'bg-purple-100 text-purple-800'];
                                                @endphp
                                                <span class="inline-block rounded px-2 py-0.5 text-xs capitalize {{ $colors[$attendance->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ $attendance->status }}
                                                </span>
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
