<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Kelola Jadwal') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Pola jadwal berulang per kelas.</p>
            </div>
            <a href="{{ route('admin.schedules.create') }}" class="btn-primary">
                + Tambah Jadwal
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
                    @if ($schedules->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada jadwal. Tambahkan pola jadwal terlebih dahulu.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Toleransi</th>
                                        <th>Periode</th>
                                        <th class="text-center">Sesi</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $schedule)
                                        <tr>
                                            <td class="font-medium text-gray-900">{{ $schedule->classRoom?->nama }}</td>
                                            <td class="capitalize">{{ $schedule->hari }}</td>
                                            <td class="text-gray-600">{{ substr($schedule->jam_mulai, 0, 5) }} - {{ substr($schedule->jam_selesai, 0, 5) }}</td>
                                            <td>{{ $schedule->toleransi }} mnt</td>
                                            <td class="text-gray-600">
                                                {{ $schedule->tanggal_mulai?->format('d M Y') }} — {{ $schedule->tanggal_selesai?->format('d M Y') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-gray-100 text-gray-700">{{ $schedule->sessions_count }}</span>
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('admin.schedules.sessions', $schedule) }}"
                                                       class="text-emerald-600 hover:text-emerald-800 font-medium">Sesi &amp; QR</a>
                                                    <a href="{{ route('admin.schedules.edit', $schedule) }}"
                                                       class="text-brand-600 hover:text-brand-800 font-medium">Edit</a>
                                                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}"
                                                          onsubmit="return confirm('Hapus jadwal ini beserta semua sesinya?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                                                    </form>
                                                </div>
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
