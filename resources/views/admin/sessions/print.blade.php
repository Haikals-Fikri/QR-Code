<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Absensi — {{ $session->classRoom?->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #f3f4f6;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 2px solid #111827;
            border-radius: 12px;
            padding: 40px 48px;
            text-align: center;
            max-width: 520px;
            width: 100%;
        }
        .card h1 { font-size: 30px; color: #111827; margin-bottom: 4px; }
        .card .meta { font-size: 15px; color: #6b7280; margin-bottom: 24px; }
        .qr { display: inline-block; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .qr svg { display: block; width: 300px; height: 300px; }
        .hint { margin-top: 20px; font-size: 14px; color: #374151; }
        .token { margin-top: 6px; font-size: 11px; color: #9ca3af; font-family: monospace; }
        .actions { margin-top: 24px; }
        .actions button {
            padding: 10px 24px; font-size: 14px; font-weight: 600;
            border: none; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .card { border-color: #000; box-shadow: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $session->classRoom?->nama }}</h1>
        <p class="meta">
            {{ $session->tanggal?->translatedFormat('l, d F Y') }} ·
            {{ substr($session->jam_mulai, 0, 5) }} - {{ substr($session->jam_selesai, 0, 5) }}
        </p>

        <div class="qr">{!! $qrSvg !!}</div>

        <p class="hint">Pindai QR ini untuk mencatat kehadiran</p>
        <p class="token">Token: {{ substr($session->qr_token, 0, 16) }}…</p>

        <div class="actions">
            <button onclick="window.print()">Cetak Halaman Ini</button>
        </div>
    </div>
</body>
</html>
