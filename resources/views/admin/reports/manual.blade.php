<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Absensi Manual (Izin / Sakit)') }}
            </h2>
            <a href="{{ route('admin.reports.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <x-alert>{{ session('status') }}</x-alert>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reports.manual.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="student_id" :value="__('Siswa')" />
                            <select id="student_id" name="student_id" required
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm">
                                <option value="">— Pilih Siswa —</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                        {{ $student->nama }} ({{ $student->nis }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="session_id" :value="__('Sesi Kelas')" />
                            <select id="session_id" name="session_id" required
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm">
                                <option value="">— Pilih Sesi —</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}" @selected(old('session_id') == $session->id)>
                                        {{ $session->tanggal?->format('d M Y') }} · {{ $session->classRoom?->nama }} · {{ substr($session->jam_mulai, 0, 5) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('session_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status Kehadiran')" />
                            <select id="status" name="status" required
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm">
                                @foreach (['hadir' => 'Hadir', 'telat' => 'Telat', 'izin' => 'Izin', 'sakit' => 'Sakit'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <p class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3">
                            Jika siswa sudah absen untuk sesi ini, data akan diperbarui (ditimpa).
                        </p>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
