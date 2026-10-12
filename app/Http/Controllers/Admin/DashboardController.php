<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ScheduleSession;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin dengan statistik ringkas.
     */
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        $sessionsToday = ScheduleSession::query()
            ->where('tanggal', $today)
            ->count();

        $attendanceToday = Attendance::query()
            ->whereHas('session', fn ($q) => $q->where('tanggal', $today))
            ->whereIn('status', ['hadir', 'telat'])
            ->count();

        // Sesi hari ini beserta jumlah kehadirannya.
        $todaySessions = ScheduleSession::query()
            ->with('classRoom:id,nama,kode')
            ->withCount('attendances')
            ->where('tanggal', $today)
            ->orderBy('jam_mulai')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'students' => Student::count(),
                'classes' => ClassRoom::count(),
                'sessions_today' => $sessionsToday,
                'attendance_today' => $attendanceToday,
            ],
            'todaySessions' => $todaySessions,
        ]);
    }
}
