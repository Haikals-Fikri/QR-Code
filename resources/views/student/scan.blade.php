<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scan QR Absensi') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Hasil scan --}}
            <div id="result" class="hidden rounded-md p-4 text-sm font-medium"></div>

            {{-- Kamera --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="reader" class="w-full rounded-md overflow-hidden bg-black"></div>

                    <div id="startArea" class="mt-4 text-center">
                        <button id="startBtn"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Aktifkan Kamera
                        </button>
                        <p class="mt-2 text-xs text-gray-500">
                            Arahkan kamera ke QR yang ditampilkan di kelas.
                        </p>
                    </div>

                    <div id="stopArea" class="mt-4 text-center hidden">
                        <button id="stopBtn"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Hentikan Kamera
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sesi hari ini --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-medium text-gray-900 mb-3">Sesi Kelas Hari Ini</h3>
                    @if ($sessions->isEmpty())
                        <p class="text-sm text-gray-500">Tidak ada sesi kelas untuk Anda hari ini.</p>
                    @else
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($sessions as $session)
                                <li class="py-2 flex items-center justify-between">
                                    <span>
                                        <span class="font-medium">{{ $session->classRoom?->nama }}</span>
                                        <span class="text-gray-500">
                                            · {{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}
                                        </span>
                                    </span>
                                    <span class="text-gray-400 text-xs">Toleransi {{ $session->toleransi }} mnt</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (function () {
            const resultBox = document.getElementById('result');
            const reader = document.getElementById('reader');
            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');
            const startArea = document.getElementById('startArea');
            const stopArea = document.getElementById('stopArea');

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                || '{{ csrf_token() }}';

            let html5QrCode = null;
            let lastToken = null;
            let cooldown = false;

            function showResult(success, message) {
                resultBox.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                resultBox.classList.add(success ? 'bg-green-100' : 'bg-red-100', success ? 'text-green-800' : 'text-red-800');
                resultBox.textContent = message;
                if (success) {
                    resultBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            async function submitToken(token) {
                // Cegah submit ganda berturut-turut dalam waktu singkat.
                if (cooldown || token === lastToken) {
                    return;
                }
                cooldown = true;
                lastToken = token;

                try {
                    const res = await fetch('{{ route('scan.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ token }),
                    });

                    const data = await res.json();
                    showResult(!!data.success, data.message || 'Terjadi kesalahan.');

                    if (data.success && html5QrCode) {
                        // Beri jeda lebih lama setelah sukses.
                        setTimeout(() => { cooldown = false; lastToken = null; }, 4000);
                        return;
                    }
                } catch (e) {
                    showResult(false, 'Gagal menghubungi server. Coba lagi.');
                }

                setTimeout(() => { cooldown = false; }, 2500);
            }

            function startScanner() {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode('reader');
                }

                html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => submitToken(decodedText.trim()),
                    () => { /* abaikan error per-frame */ }
                ).then(() => {
                    startArea.classList.add('hidden');
                    stopArea.classList.remove('hidden');
                }).catch((err) => {
                    showResult(false, 'Tidak dapat mengakses kamera: ' + err);
                });
            }

            function stopScanner() {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        startArea.classList.remove('hidden');
                        stopArea.classList.add('hidden');
                    });
                }
            }

            startBtn.addEventListener('click', startScanner);
            stopBtn.addEventListener('click', stopScanner);
        })();
    </script>
</x-app-layout>
