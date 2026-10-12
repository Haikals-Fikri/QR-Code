<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Kehadiran — {{ $class->nama }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #111827; }
        h1 { font-size: 16px; margin: 0 0 2px 0; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; }
        th { background: #f3f4f6; text-align: left; }
        td.center, th.center { text-align: center; }
        .footer { margin-top: 10px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Rekap Kehadiran — {{ $class->nama }}</h1>
    <p class="meta">
        Kode: {{ $class->kode }} · Total {{ $totalSesi }} sesi · {{ $students->count() }} siswa ·
        Dicetak: {{ now()->translatedFormat('d F Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th class="center">Hadir</th>
                <th class="center">Telat</th>
                <th class="center">Izin</th>
                <th class="center">Sakit</th>
                <th class="center">Alpha</th>
                <th class="center">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $row)
                <tr>
                    <td>{{ $row['nis'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td class="center">{{ $row['hadir'] }}</td>
                    <td class="center">{{ $row['telat'] }}</td>
                    <td class="center">{{ $row['izin'] }}</td>
                    <td class="center">{{ $row['sakit'] }}</td>
                    <td class="center">{{ $row['alpha'] }}</td>
                    <td class="center">{{ $row['persen_hadir'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="8" class="center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Alpha dihitung otomatis: total sesi − (hadir + telat + izin + sakit).</p>
</body>
</html>
