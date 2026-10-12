<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ScheduleSessionController extends Controller
{
    /**
     * Tampilkan halaman QR untuk satu sesi (untuk proyektor/layar).
     */
    public function show(ScheduleSession $session): View
    {
        $session->load('classRoom:id,nama,kode');

        // QR berisi token sesi; siswa akan memindai ini dari layar.
        $qrSvg = QrCode::format('svg')
            ->size(360)
            ->margin(1)
            ->generate($session->qr_token);

        return view('admin.sessions.show', [
            'session' => $session,
            'qrSvg' => $qrSvg,
        ]);
    }

    /**
     * Halaman QR siap-cetak (untuk ditempel).
     */
    public function print(ScheduleSession $session): View
    {
        $session->load('classRoom:id,nama,kode');

        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($session->qr_token);

        return view('admin.sessions.print', [
            'session' => $session,
            'qrSvg' => $qrSvg,
        ]);
    }

    /**
     * Regenerate token QR sebuah sesi.
     */
    public function regenerate(ScheduleSession $session): RedirectResponse
    {
        $session->update([
            'qr_token' => bin2hex(random_bytes(32)),
        ]);

        return back()->with('status', 'Token QR berhasil di-regenerate.');
    }

    /**
     * Generate sesi secara manual untuk sebuah pola jadwal.
     */
    public function generate(Schedule $schedule): RedirectResponse
    {
        Artisan::call('schedule:generate', [
            '--schedule' => $schedule->id,
        ]);

        return redirect()
            ->route('admin.schedules.sessions', $schedule)
            ->with('status', 'Sesi berhasil digenerate (30 hari ke depan).');
    }
}
