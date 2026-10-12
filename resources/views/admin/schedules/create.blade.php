<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Tambah Jadwal" subtitle="Buat pola jadwal berulang." />
    </x-slot>

    <div class="max-w-3xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.schedules.store') }}">
                    @csrf
                    @include('admin.schedules.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
