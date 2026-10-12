<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Rekap Siswa') }} — {{ $student->nama }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">NIS {{ $student->nis }}</p>
            </div>
            <a href="{{ url()->previous() }}" class="btn-secondary">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">
            {{-- Ringkasan --}}
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                @foreach (['total_sesi' => 'Sesi', 'hadir' => 'Hadir', 'telat' => 'Telat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $key => $label)
                    <div class="card p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $summary[$key] }}</p>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <div class="card card-body text-center">
                <span class="text-sm text-gray-500">Persentase Kehadiran (Hadir + Telat):</span>
                <span class="text-lg font-bold text-brand-600 ml-2">{{ $summary['persen_hadir'] }}%</span>
            </div>

            {{-- Riwayat --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="font-semibold text-gray-900 mb-3">Riwayat Absensi</h3>
                    @if ($attendances->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada catatan absensi.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kelas</th>
                                        <th>Waktu Scan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $attendance)
                                        <tr>
                                            <td class="text-gray-700">{{ $attendance->session?->tanggal?->format('d M Y') }}</td>
                                            <td class="text-gray-700">{{ $attendance->session?->classRoom?->nama }}</td>
                                            <td class="text-gray-500">{{ $attendance->waktu_scan?->format('H:i:s') }}</td>
                                            <td>
                                                <span class="badge-{{ $attendance->status }}">{{ ucfirst($attendance->status) }}</span>
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
