<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Kelola Kelas') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Total {{ $classes->count() }} kelas.</p>
            </div>
            <a href="{{ route('admin.classes.create') }}" class="btn-primary">
                + Tambah Kelas
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
                    @if ($classes->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada kelas. Tambahkan kelas terlebih dahulu.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Kelas</th>
                                        <th>Guru</th>
                                        <th class="text-center">Jml Siswa</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classes as $class)
                                        <tr>
                                            <td class="font-mono text-gray-700">{{ $class->kode }}</td>
                                            <td class="font-medium text-gray-900">{{ $class->nama }}</td>
                                            <td class="text-gray-600">{{ $class->guru?->name ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-gray-100 text-gray-700">{{ $class->students_count }}</span>
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('admin.classes.edit', $class) }}"
                                                       class="text-brand-600 hover:text-brand-800 font-medium">Edit</a>
                                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                                          onsubmit="return confirm('Hapus kelas ini? Semua jadwal & absensi terkait akan ikut terhapus.');">
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
