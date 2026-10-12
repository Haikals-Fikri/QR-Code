@php($classRoom = $classRoom ?? null)

<div class="space-y-6">
    <div>
        <x-input-label for="nama" :value="__('Nama Kelas')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full"
                      :value="old('nama', $classRoom?->nama)" required autofocus />
        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="kode" :value="__('Kode Kelas')" />
        <x-text-input id="kode" name="kode" type="text" class="mt-1 block w-full"
                      :value="old('kode', $classRoom?->kode)" required />
        <x-input-error :messages="$errors->get('kode')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="guru_id" :value="__('Guru Pengajar (opsional)')" />
        <select id="guru_id" name="guru_id"
                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm">
            <option value="">— Tidak ada —</option>
            @foreach ($gurus as $id => $name)
                <option value="{{ $id }}" @selected(old('guru_id', $classRoom?->guru_id) == $id)>{{ $name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('guru_id')" class="mt-2" />
    </div>
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Simpan') }}</x-primary-button>
    <a href="{{ route('admin.classes.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Batal</a>
</div>
