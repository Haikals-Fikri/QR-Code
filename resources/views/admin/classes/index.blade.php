<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Kelas') }}
            </h2>
            <a href="{{ route('admin.classes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                + Tambah Kelas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($classes->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada kelas. Tambahkan kelas terlebih dahulu.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="px-4 py-3 font-medium">Kode</th>
                                        <th class="px-4 py-3 font-medium">Nama Kelas</th>
                                        <th class="px-4 py-3 font-medium">Guru</th>
                                        <th class="px-4 py-3 font-medium">Jml Siswa</th>
                                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($classes as $class)
                                        <tr>
                                            <td class="px-4 py-3 font-mono text-gray-700">{{ $class->kode }}</td>
                                            <td class="px-4 py-3">{{ $class->nama }}</td>
                                            <td class="px-4 py-3">{{ $class->guru?->name ?? '-' }}</td>
                                            <td class="px-4 py-3">{{ $class->students_count }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('admin.classes.edit', $class) }}"
                                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                                          onsubmit="return confirm('Hapus kelas ini? Semua jadwal & absensi terkait akan ikut terhapus.');">
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
