<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit Kelas" subtitle="Perbarui data kelas." />
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.classes.update', $classRoom) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.classes.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
