<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Dasbor" :subtitle="'Selamat datang kembali, ' . Auth::user()->name . '.'" />
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.reports.index') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Lihat Laporan
        </a>
    </x-slot>

    <div class="space-y-6">
        {{-- Kartu statistik --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Siswa" :value="$stats['students']" tone="brand"
                :href="route('admin.students.index')"
                icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.36-1.86M17 20H7m10 0v-2c0-.65-.13-1.28-.36-1.86m0 0a5.97 5.97 0 00-3.64-1.24M7 20H2v-2a3 3 0 015.36-1.86M7 20v-2c0-.65.13-1.28.36-1.86m0 0A6 6 0 0112 14a6 6 0 014.64 2.14M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' />

            <x-stat-card label="Total Kelas" :value="$stats['classes']" tone="purple"
                :href="route('admin.classes.index')"
                icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14zm0 0v7"/></svg>' />

            <x-stat-card label="Sesi Hari Ini" :value="$stats['sessions_today']" tone="amber"
                :href="route('admin.schedules.index')"
                icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' />

            <x-stat-card label="Hadir Hari Ini" :value="$stats['attendance_today']" tone="emerald"
                :href="route('admin.reports.index')"
                icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Sesi hari ini --}}
            <div class="lg:col-span-2 card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Sesi Kelas Hari Ini</h3>
                        <a href="{{ route('admin.schedules.index') }}" class="text-sm text-brand-600 hover:text-brand-800">Kelola Jadwal</a>
                    </div>

                    @if ($todaySessions->isEmpty())
                        <p class="text-sm text-gray-500">Tidak ada sesi kelas hari ini.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Jam</th>
                                        <th class="text-center">Hadir</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($todaySessions as $session)
                                        <tr>
                                            <td>
                                                <span class="font-medium text-gray-900">{{ $session->classRoom?->nama }}</span>
                                                <span class="text-xs text-gray-400 font-mono">{{ $session->classRoom?->kode }}</span>
                                            </td>
                                            <td class="text-gray-600">{{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}</td>
                                            <td class="text-center">
                                                <span class="badge-hadir">{{ $session->attendances_count }}</span>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.sessions.show', $session) }}" class="text-brand-600 hover:text-brand-800 font-medium">QR</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Aksi cepat --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.students.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-brand-300 hover:bg-brand-50/50 transition">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600">＋</span>
                            <span class="text-sm font-medium text-gray-700">Tambah Siswa</span>
                        </a>
                        <a href="{{ route('admin.students.import.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-brand-300 hover:bg-brand-50/50 transition">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600">⬆</span>
                            <span class="text-sm font-medium text-gray-700">Import Siswa</span>
                        </a>
                        <a href="{{ route('admin.schedules.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-brand-300 hover:bg-brand-50/50 transition">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600">🗓</span>
                            <span class="text-sm font-medium text-gray-700">Buat Jadwal</span>
                        </a>
                        <a href="{{ route('admin.reports.manual.form') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-brand-300 hover:bg-brand-50/50 transition">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600">🖊</span>
                            <span class="text-sm font-medium text-gray-700">Absensi Manual</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
