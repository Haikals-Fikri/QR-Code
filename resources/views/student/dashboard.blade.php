<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-2 font-medium">Halo, {{ Auth::user()->student?->nama ?? Auth::user()->name }}.</p>
                    <p class="text-sm text-gray-600">
                        Di sini nanti kamu bisa scan QR kelas dan melihat rekap kehadiranmu.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
