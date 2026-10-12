<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ScheduleSession;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class AttendanceScanner
{
    /**
     * Proses pemindaian QR untuk seorang siswa.
     *
     * Urutan validasi:
     * 1. Token cocok ke sesi aktif.
     * 2. Siswa terdaftar di kelas sesi.
     * 3. Window waktu (belum waktunya / sudah selesai).
     * 4. Tentukan status (hadir / telat).
     * 5. Anti duplikat (unique student+session).
     */
    public function scan(Student $student, string $qrToken, ?Carbon $now = null): ScanResult
    {
        $now ??= Carbon::now();

        // 1. Cari sesi berdasarkan token.
        $session = ScheduleSession::query()
            ->where('qr_token', $qrToken)
            ->where('qr_token_aktif', true)
            ->first();

        if (! $session) {
            return ScanResult::failure('QR tidak dikenali atau sudah tidak berlaku.');
        }

        // 2. Pastikan siswa terdaftar di kelas sesi.
        $terdaftar = $student->classes()->where('classes.id', $session->class_id)->exists();

        if (! $terdaftar) {
            return ScanResult::failure('Anda tidak terdaftar pada kelas ini.');
        }

        // 3. Window waktu.
        $tanggal = $session->tanggal->toDateString();
        $mulai = Carbon::parse($tanggal.' '.$session->jam_mulai);
        $selesai = Carbon::parse($tanggal.' '.$session->jam_selesai);
        $batasAwal = $mulai->copy()->subMinutes($session->toleransi);

        if ($now->lessThan($batasAwal)) {
            return ScanResult::failure('Belum waktunya absen untuk sesi ini.');
        }

        if ($now->greaterThan($selesai)) {
            return ScanResult::failure('Sesi ini sudah selesai.');
        }

        // 5. Anti duplikat — cek dulu untuk pesan ramah.
        $sudahAda = Attendance::query()
            ->where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->exists();

        if ($sudahAda) {
            return ScanResult::failure('Anda sudah absen pada sesi ini.');
        }

        // 4. Tentukan status.
        $batasTelat = $mulai->copy()->addMinutes($session->toleransi);
        $status = $now->lessThanOrEqualTo($batasTelat) ? 'hadir' : 'telat';

        try {
            $attendance = DB::transaction(fn () => Attendance::create([
                'student_id' => $student->id,
                'session_id' => $session->id,
                'waktu_scan' => $now,
                'status' => $status,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]));
        } catch (UniqueConstraintViolationException) {
            // Race condition: sudah ada record dari request paralel.
            return ScanResult::failure('Anda sudah absen pada sesi ini.');
        }

        $label = $status === 'hadir' ? 'Hadir' : 'Telat';

        return ScanResult::success("Absensi berhasil! Status: {$label}.", $attendance);
    }
}
