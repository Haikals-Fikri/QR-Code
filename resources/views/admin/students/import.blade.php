<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('import_errors'))
                <div class="p-4 bg-red-50 text-red-800 rounded-md text-sm">
                    <p class="font-medium mb-2">Baris yang gagal:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <h3 class="font-medium mb-2">Petunjuk</h3>
                        <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                            <li>Kolom yang wajib: <code class="bg-gray-100 px-1 rounded">nis</code>, <code class="bg-gray-100 px-1 rounded">nama</code>, <code class="bg-gray-100 px-1 rounded">email</code>.</li>
                            <li>Kolom opsional: <code class="bg-gray-100 px-1 rounded">phone</code>, <code class="bg-gray-100 px-1 rounded">kode_kelas</code>.</li>
                            <li>Password awal akun = <strong>NIS</strong>.</li>
                            <li><code class="bg-gray-100 px-1 rounded">kode_kelas</code> harus sesuai kode kelas yang sudah ada.</li>
                        </ul>
                    </div>

                    <div>
                        <a href="{{ route('admin.students.import.template') }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            ⬇ Unduh Template (.xlsx)
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="file" :value="__('File (xlsx / xls / csv)')" />
                            <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv,.txt"
                                   class="mt-1 block w-full text-sm border border-gray-300 rounded-md p-2">
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Import') }}</x-primary-button>
                            <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali ke daftar siswa</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
