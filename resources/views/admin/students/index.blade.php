<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Kelola Siswa" :subtitle="'Total ' . $students->count() . ' siswa terdaftar.'" />
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.students.import.create') }}" class="btn-secondary">Import</a>
        <a href="{{ route('admin.students.create') }}" class="btn-primary">+ Tambah Siswa</a>
    </x-slot>

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
</x-app-layout>
