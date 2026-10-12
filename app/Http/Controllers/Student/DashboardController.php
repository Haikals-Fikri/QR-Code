<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ScheduleSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard siswa.
     */
    public function index(Request $request): View
    {
        $student = $request->user()->student;

        if (! $student) {
            return view('student.dashboard', [
                'student' => null,
                'sessionsToday' => collect(),
                'summary' => null,
            ]);
        }

        $classIds = $student->classes()->pluck('classes.id');

        $sessionsToday = $classIds->isEmpty()
            ? collect()
            : ScheduleSession::query()
                ->with('classRoom:id,nama,kode')
                ->whereIn('class_id', $classIds)
                ->where('tanggal', Carbon::today()->toDateString())
                ->orderBy('jam_mulai')
                ->get();

        $attendances = $student->attendances()->get();

        $summary = [
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'telat' => $attendances->where('status', 'telat')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
        ];

        return view('student.dashboard', compact('student', 'sessionsToday', 'summary'));
    }
}
