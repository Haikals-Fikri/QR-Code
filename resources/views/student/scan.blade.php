<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Scan QR Absensi') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8 space-y-5">
            {{-- Hasil scan — banner besar. --}}
            <div id="result" class="hidden rounded-card p-5 text-base font-semibold flex items-start gap-3"></div>

            {{-- Kamera — fokus utama (kiosk). --}}
            <div class="card overflow-hidden">
                <div id="reader" class="w-full bg-black aspect-square"></div>

                <div class="p-6 text-center">
                    <div id="startArea">
                        <button id="startBtn" class="btn-primary w-full py-4 text-base">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.55-2.27A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Aktifkan Kamera
                        </button>
                        <p class="mt-3 text-sm text-gray-500">
                            Arahkan kamera ke QR yang ditampilkan di kelas.
                        </p>
                    </div>

                    <div id="stopArea" class="hidden">
                        <button id="stopBtn" class="btn-secondary w-full py-3">
                            Hentikan Kamera
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sesi hari ini --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="font-semibold text-gray-900 mb-3">Sesi Kelas Hari Ini</h3>
                    @if ($sessions->isEmpty())
                        <p class="text-sm text-gray-500">Tidak ada sesi kelas untuk Anda hari ini.</p>
                    @else
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($sessions as $session)
                                <li class="py-3 flex items-center justify-between">
                                    <span class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full bg-brand-500"></span>
                                        <span class="font-medium text-gray-900">{{ $session->classRoom?->nama }}</span>
                                    </span>
                                    <span class="text-gray-500 text-xs">
                                        {{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}
                                        · toleransi {{ $session->toleransi }} mnt
                                    </span>
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

            const ICONS = {
                success: '<svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                error: '<svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>',
            };

            function showResult(success, message) {
                resultBox.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200',
                    'bg-red-50', 'text-red-800', 'border-red-200');
                resultBox.classList.add('border',
                    success ? 'bg-emerald-50' : 'bg-red-50',
                    success ? 'text-emerald-800' : 'text-red-800',
                    success ? 'border-emerald-200' : 'border-red-200');
                resultBox.innerHTML = (success ? ICONS.success : ICONS.error) + '<span>' + message + '</span>';
                resultBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            async function submitToken(token) {
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

                    if (data.success) {
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
