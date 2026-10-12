<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit Siswa" subtitle="Perbarui data siswa & kelas." />
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.students.update', $student) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.students.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
