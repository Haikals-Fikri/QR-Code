@php($schedule = $schedule ?? null)

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="sm:col-span-2">
        <x-input-label for="class_id" :value="__('Kelas')" />
        <select id="class_id" name="class_id"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="">— Pilih Kelas —</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('class_id', $schedule?->class_id) == $class->id)>
                    {{ $class->nama }} ({{ $class->kode }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('class_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="hari" :value="__('Hari')" />
        <select id="hari" name="hari"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
                <option value="{{ $hari }}" @selected(old('hari', $schedule?->hari) === $hari)>
                    {{ ucfirst($hari) }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('hari')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="toleransi" :value="__('Toleransi Telat (menit)')" />
        <x-text-input id="toleransi" name="toleransi" type="number" min="0" max="120"
                      class="mt-1 block w-full" :value="old('toleransi', $schedule?->toleransi ?? 15)" required />
        <x-input-error :messages="$errors->get('toleransi')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
        <x-text-input id="jam_mulai" name="jam_mulai" type="time"
                      class="mt-1 block w-full" :value="old('jam_mulai', $schedule?->jam_mulai ? substr($schedule->jam_mulai, 0, 5) : '08:00')" required />
        <x-input-error :messages="$errors->get('jam_mulai')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
        <x-text-input id="jam_selesai" name="jam_selesai" type="time"
                      class="mt-1 block w-full" :value="old('jam_selesai', $schedule?->jam_selesai ? substr($schedule->jam_selesai, 0, 5) : '10:00')" required />
        <x-input-error :messages="$errors->get('jam_selesai')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="tanggal_mulai" :value="__('Berlaku Dari')" />
        <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date"
                      class="mt-1 block w-full" :value="old('tanggal_mulai', $schedule?->tanggal_mulai?->toDateString() ?? now()->toDateString())" required />
        <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="tanggal_selesai" :value="__('Berlaku Sampai')" />
        <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date"
                      class="mt-1 block w-full" :value="old('tanggal_selesai', $schedule?->tanggal_selesai?->toDateString() ?? now()->addMonths(3)->toDateString())" required />
        <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
    </div>
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Simpan') }}</x-primary-button>
    <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>
