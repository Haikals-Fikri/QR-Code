<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Tambah Kelas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.classes.store') }}">
                        @csrf
                        @include('admin.classes.partials.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
