<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Jadwal') }}
            </h2>
            <a href="{{ route('admin.schedules.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Tambah Jadwal
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
                    @if ($schedules->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada jadwal. Tambahkan pola jadwal terlebih dahulu.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="px-4 py-3 font-medium">Kelas</th>
                                        <th class="px-4 py-3 font-medium">Hari</th>
                                        <th class="px-4 py-3 font-medium">Jam</th>
                                        <th class="px-4 py-3 font-medium">Toleransi</th>
                                        <th class="px-4 py-3 font-medium">Periode</th>
                                        <th class="px-4 py-3 font-medium">Sesi</th>
                                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($schedules as $schedule)
                                        <tr>
                                            <td class="px-4 py-3">{{ $schedule->classRoom?->nama }}</td>
                                            <td class="px-4 py-3 capitalize">{{ $schedule->hari }}</td>
                                            <td class="px-4 py-3">{{ substr($schedule->jam_mulai, 0, 5) }} - {{ substr($schedule->jam_selesai, 0, 5) }}</td>
                                            <td class="px-4 py-3">{{ $schedule->toleransi }} mnt</td>
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ $schedule->tanggal_mulai?->format('d M Y') }} — {{ $schedule->tanggal_selesai?->format('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3">{{ $schedule->sessions_count }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('admin.schedules.sessions', $schedule) }}"
                                                       class="text-emerald-600 hover:text-emerald-900">Sesi &amp; QR</a>
                                                    <a href="{{ route('admin.schedules.edit', $schedule) }}"
                                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}"
                                                          onsubmit="return confirm('Hapus jadwal ini beserta semua sesinya?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
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
