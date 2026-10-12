@php($student = $student ?? null)

<div class="space-y-6">
    <div>
        <x-input-label for="nis" :value="__('NIS / NIM')" />
        <x-text-input id="nis" name="nis" type="text" class="mt-1 block w-full"
                      :value="old('nis', $student?->nis)" required autofocus />
        <x-input-error :messages="$errors->get('nis')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="nama" :value="__('Nama Lengkap')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full"
                      :value="old('nama', $student?->nama)" required />
        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                      :value="old('email', $student?->user?->email)" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
        @unless ($student)
            <p class="mt-1 text-xs text-gray-500">Password awal akun = NIS. Sarankan siswa menggantinya setelah login.</p>
        @endunless
    </div>

    <div>
        <x-input-label for="phone" :value="__('No. HP (opsional)')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                      :value="old('phone', $student?->phone)" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div>
        <x-input-label :value="__('Kelas yang Diikuti')" />
        @php($selectedClasses = old('classes', $student?->classes->pluck('id')->all() ?? []))
        @if ($classes->isEmpty())
            <p class="mt-1 text-sm text-gray-500">Belum ada kelas. Tambahkan kelas terlebih dahulu.</p>
        @else
            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach ($classes as $class)
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 rounded-lg border border-gray-200 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="classes[]" value="{{ $class->id }}"
                               @checked(in_array($class->id, $selectedClasses))
                               class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        {{ $class->nama }} <span class="text-gray-400 font-mono text-xs">({{ $class->kode }})</span>
                    </label>
                @endforeach
            </div>
        @endif
        <x-input-error :messages="$errors->get('classes')" class="mt-2" />
    </div>
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Simpan') }}</x-primary-button>
    <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Batal</a>
</div>
