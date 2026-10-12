<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
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
                <a href="{{ route('admin.schedules.index') }}" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <x-alert>{{ session('status') }}</x-alert>
            @endif

            <div class="card">
                <div class="card-body">
                    @if ($sessions->isEmpty())
                        <p class="text-sm text-gray-500">
                            Belum ada sesi. Klik <strong>Generate Sesi</strong> untuk membuat sesi + QR untuk 30 hari ke depan.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Token</th>
                                        <th>Status</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td class="font-medium text-gray-900">{{ $session->tanggal?->format('d M Y') }}</td>
                                            <td class="text-gray-600">{{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}</td>
                                            <td class="font-mono text-xs text-gray-400">{{ substr($session->qr_token, 0, 12) }}…</td>
                                            <td>
                                                @if ($session->qr_token_aktif)
                                                    <span class="badge bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Aktif</span>
                                                @else
                                                    <span class="badge bg-gray-100 text-gray-600">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('admin.sessions.show', $session) }}"
                                                       class="text-brand-600 hover:text-brand-800 font-medium">Tampilkan QR</a>
                                                    <a href="{{ route('admin.sessions.print', $session) }}" target="_blank"
                                                       class="text-gray-600 hover:text-gray-900 font-medium">Cetak</a>
                                                    <form method="POST" action="{{ route('admin.sessions.regenerate', $session) }}"
                                                          onsubmit="return confirm('Regenerate token QR sesi ini? Token lama tidak berlaku lagi.');">
                                                        @csrf
                                                        <button type="submit" class="text-amber-600 hover:text-amber-800 font-medium">Regenerate</button>
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
