<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ScheduleSession;
use App\Models\Student;
use Illuminate\Support\Collection;

class AttendanceReport
{
    /**
     * Rekap kehadiran semua siswa dalam satu kelas.
     *
     * @return array{total_sesi: int, students: Collection<int, array<string, mixed>>}
     */
    public function forClass(ClassRoom $class): array
    {
        $totalSesi = ScheduleSession::query()
            ->where('class_id', $class->id)
            ->count();

        $students = $class->students()
            ->orderBy('nama')
            ->get()
            ->map(fn (Student $student) => $this->summarize($student, $totalSesi, $class));

        return [
            'total_sesi' => $totalSesi,
            'students' => $students,
        ];
    }

    /**
     * Rekap ringkas satu siswa (opsional dibatasi ke satu kelas).
     *
     * @return array<string, int|float>
     */
    public function summarize(Student $student, ?int $totalSesi = null, ?ClassRoom $class = null): array
    {
        $classIds = $class
            ? collect([$class->id])
            : $student->classes()->pluck('classes.id');

        if ($totalSesi === null) {
            $totalSesi = $classIds->isEmpty()
                ? 0
                : ScheduleSession::query()->whereIn('class_id', $classIds)->count();
        }

        $counts = Attendance::query()
            ->where('student_id', $student->id)
            ->whereHas('session', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            })
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($counts['hadir'] ?? 0);
        $telat = (int) ($counts['telat'] ?? 0);
        $izin = (int) ($counts['izin'] ?? 0);
        $sakit = (int) ($counts['sakit'] ?? 0);
        $alpha = max(0, $totalSesi - ($hadir + $telat + $izin + $sakit));

        return [
            'student_id' => $student->id,
            'nis' => $student->nis,
            'nama' => $student->nama,
            'total_sesi' => $totalSesi,
            'hadir' => $hadir,
            'telat' => $telat,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpha' => $alpha,
            'persen_hadir' => $totalSesi > 0
                ? round(($hadir + $telat) / $totalSesi * 100, 1)
                : 0.0,
        ];
    }
}
