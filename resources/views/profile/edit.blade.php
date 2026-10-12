<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Profil" subtitle="Kelola informasi akun Anda." />
    </x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="card">
            <div class="card-body max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="card-body max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card">
            <div class="card-body max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
