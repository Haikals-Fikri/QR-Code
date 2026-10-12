<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Tambah Jadwal') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.schedules.store') }}">
                        @csrf
                        @include('admin.schedules.partials.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
