<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Sesi & QR') }} — {{ $schedule->classRoom?->nama }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ucfirst($schedule->hari) }}, {{ substr($schedule->jam_mulai, 0, 5) }} - {{ substr($schedule->jam_selesai, 0, 5) }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.schedules.generate', $schedule) }}">
                    @csrf
                    <x-primary-button>{{ __('Generate Sesi (30 hari)') }}</x-primary-button>
                </form>
                <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($sessions->isEmpty())
                        <p class="text-sm text-gray-500">
                            Belum ada sesi. Klik <strong>Generate Sesi</strong> untuk membuat sesi + QR untuk 30 hari ke depan.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="px-4 py-3 font-medium">Tanggal</th>
                                        <th class="px-4 py-3 font-medium">Jam</th>
                                        <th class="px-4 py-3 font-medium">Token</th>
                                        <th class="px-4 py-3 font-medium">Status</th>
                                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td class="px-4 py-3">{{ $session->tanggal?->format('d M Y') }}</td>
                                            <td class="px-4 py-3">{{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}</td>
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ substr($session->qr_token, 0, 12) }}…</td>
                                            <td class="px-4 py-3">
                                                @if ($session->qr_token_aktif)
                                                    <span class="inline-block bg-green-100 text-green-800 rounded px-2 py-0.5 text-xs">Aktif</span>
                                                @else
                                                    <span class="inline-block bg-gray-100 text-gray-600 rounded px-2 py-0.5 text-xs">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('admin.sessions.show', $session) }}"
                                                       class="text-emerald-600 hover:text-emerald-900">Tampilkan QR</a>
                                                    <a href="{{ route('admin.sessions.print', $session) }}" target="_blank"
                                                       class="text-indigo-600 hover:text-indigo-900">Cetak</a>
                                                    <form method="POST" action="{{ route('admin.sessions.regenerate', $session) }}"
                                                          onsubmit="return confirm('Regenerate token QR sesi ini? Token lama tidak berlaku lagi.');">
                                                        @csrf
                                                        <button type="submit" class="text-amber-600 hover:text-amber-900">Regenerate</button>
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
