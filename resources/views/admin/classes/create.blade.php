<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Tambah Kelas" subtitle="Data kelas baru." />
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.classes.store') }}">
                    @csrf
                    @include('admin.classes.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
