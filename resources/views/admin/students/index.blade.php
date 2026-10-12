<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Siswa') }}
            </h2>
            <a href="{{ route('admin.students.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                + Tambah Siswa
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
                    @if ($students->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada siswa.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="px-4 py-3 font-medium">NIS</th>
                                        <th class="px-4 py-3 font-medium">Nama</th>
                                        <th class="px-4 py-3 font-medium">Email</th>
                                        <th class="px-4 py-3 font-medium">Kelas</th>
                                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($students as $student)
                                        <tr>
                                            <td class="px-4 py-3 font-mono text-gray-700">{{ $student->nis }}</td>
                                            <td class="px-4 py-3">{{ $student->nama }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $student->user?->email }}</td>
                                            <td class="px-4 py-3">
                                                @forelse ($student->classes as $class)
                                                    <span class="inline-block bg-gray-100 text-gray-700 rounded px-2 py-0.5 text-xs mb-1">
                                                        {{ $class->nama }}
                                                    </span>
                                                @empty
                                                    <span class="text-gray-400">-</span>
                                                @endforelse
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('admin.students.edit', $student) }}"
                                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                                                          onsubmit="return confirm('Hapus siswa ini beserta akun login-nya?');">
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
