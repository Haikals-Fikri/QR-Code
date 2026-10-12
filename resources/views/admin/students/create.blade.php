<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Tambah Siswa" subtitle="Akun login dibuat otomatis (password = NIS)." />
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.students.store') }}">
                    @csrf
                    @include('admin.students.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
