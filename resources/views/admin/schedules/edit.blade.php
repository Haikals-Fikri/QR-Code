<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit Jadwal" subtitle="Perbarui pola jadwal." />
    </x-slot>

    <div class="max-w-3xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.schedules.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
