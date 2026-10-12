<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\ScanRequest;
use App\Models\ScheduleSession;
use App\Models\Student;
use App\Services\AttendanceScanner;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ScanController extends Controller
{
    /**
     * Tampilkan halaman scan QR.
     */
    public function index(): View
    {
        $student = request()->user()->student;

        $sessions = collect();

        if ($student) {
            $sessions = $this->todaySessionsFor($student);
        }

        return view('student.scan', compact('sessions'));
    }

    /**
     * Proses token QR yang dikirim dari halaman scan.
     */
    public function store(ScanRequest $request, AttendanceScanner $scanner): JsonResponse
    {
        $student = $request->user()->student;

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum memiliki profil siswa.',
            ], 422);
        }

        $result = $scanner->scan($student, $request->validated('token'));

        return response()->json([
            'success' => $result->success,
            'message' => $result->message,
            'status' => $result->attendance?->status,
            'waktu_scan' => $result->attendance?->waktu_scan?->format('H:i:s'),
        ], $result->success ? 200 : 422);
    }

    /**
     * Sesi hari ini untuk kelas-kelas yang diikuti siswa.
     *
     * @return Collection<int, ScheduleSession>
     */
    private function todaySessionsFor(Student $student): Collection
    {
        $classIds = $student->classes()->pluck('classes.id');

        if ($classIds->isEmpty()) {
            return collect();
        }

        return ScheduleSession::query()
            ->with('classRoom:id,nama,kode')
            ->whereIn('class_id', $classIds)
            ->where('tanggal', Carbon::today()->toDateString())
            ->orderBy('jam_mulai')
            ->get();
    }
}
