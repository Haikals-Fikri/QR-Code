<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Import Siswa') }}
            </h2>
            <a href="{{ route('admin.students.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <x-alert>{{ session('status') }}</x-alert>
            @endif

            @if (session('import_errors'))
                <x-alert type="error">
                    <p class="font-semibold mb-2">Baris yang gagal:</p>
                    <ul class="list-disc list-inside space-y-1 font-normal">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <div class="card">
                <div class="card-body space-y-6">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Petunjuk</h3>
                        <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                            <li>Kolom wajib: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">nis</code>, <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">nama</code>, <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">email</code>.</li>
                            <li>Kolom opsional: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">phone</code>, <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">kode_kelas</code>.</li>
                            <li>Password awal akun = <strong>NIS</strong>.</li>
                            <li><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">kode_kelas</code> harus sesuai kode kelas yang sudah ada (otomatis mendaftarkan siswa).</li>
                            <li>Baris valid tetap diimport meski ada baris lain yang gagal.</li>
                        </ul>
                    </div>

                    <div>
                        <a href="{{ route('admin.students.import.template') }}" class="btn-secondary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/></svg>
                            Unduh Template (.xlsx)
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data" class="space-y-4 border-t border-gray-100 pt-6">
                        @csrf

                        <div>
                            <x-input-label for="file" :value="__('File (xlsx / xls / csv)')" />
                            <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv,.txt"
                                   class="mt-1 block w-full text-sm border border-gray-300 rounded-lg p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand-50 file:text-brand-700 file:font-medium">
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Import') }}</x-primary-button>
                            <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Kembali ke daftar siswa</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
