<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Kelola Siswa') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Total {{ $students->count() }} siswa terdaftar.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.students.import.create') }}" class="btn-secondary">
                    Import
                </a>
                <a href="{{ route('admin.students.create') }}" class="btn-primary">
                    + Tambah Siswa
                </a>
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
                    @if ($students->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada siswa. Tambahkan siswa atau import dari file.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th>NIS</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Kelas</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                        <tr>
                                            <td class="font-mono text-gray-700">{{ $student->nis }}</td>
                                            <td class="font-medium text-gray-900">{{ $student->nama }}</td>
                                            <td class="text-gray-600">{{ $student->user?->email }}</td>
                                            <td>
                                                <div class="flex flex-wrap gap-1">
                                                    @forelse ($student->classes as $class)
                                                        <span class="inline-block bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-600/20 rounded-full px-2 py-0.5 text-xs font-medium">
                                                            {{ $class->nama }}
                                                        </span>
                                                    @empty
                                                        <span class="text-gray-400">-</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('admin.students.edit', $student) }}"
                                                       class="text-brand-600 hover:text-brand-800 font-medium">Edit</a>
                                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                                                          onsubmit="return confirm('Hapus siswa ini beserta akun login-nya?');">
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
