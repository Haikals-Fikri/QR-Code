<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClassAttendanceExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManualAttendanceRequest;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ScheduleSession;
use App\Models\Student;
use App\Services\AttendanceReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * Daftar kelas untuk dipilih pada laporan.
     */
    public function index(): View
    {
        $classes = ClassRoom::query()
            ->withCount(['students', 'sessions'])
            ->orderBy('nama')
            ->get();

        return view('admin.reports.index', compact('classes'));
    }

    /**
     * Rekap kehadiran per kelas.
     */
    public function classReport(ClassRoom $class, AttendanceReport $report): View
    {
        $class->load('students:id,nama,nis');

        $data = $report->forClass($class);

        return view('admin.reports.class', [
            'class' => $class,
            'totalSesi' => $data['total_sesi'],
            'students' => $data['students'],
        ]);
    }

    /**
     * Rekap detail per siswa (semua kelas).
     */
    public function studentReport(Student $student, AttendanceReport $report): View
    {
        $student->load('classes:id,nama,kode');

        $summary = $report->summarize($student);

        $attendances = $student->attendances()
            ->with('session.classRoom:id,nama')
            ->latest('waktu_scan')
            ->get();

        return view('admin.reports.student', compact('student', 'summary', 'attendances'));
    }

    /**
     * Form input absensi manual (izin/sakit/hadir/telat).
     */
    public function manualForm(Request $request): View
    {
        $students = Student::query()->orderBy('nama')->get();

        $sessions = ScheduleSession::query()
            ->with('classRoom:id,nama')
            ->orderByDesc('tanggal')
            ->limit(200)
            ->get();

        return view('admin.reports.manual', compact('students', 'sessions'));
    }

    /**
     * Simpan absensi manual.
     */
    public function manualStore(StoreManualAttendanceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Attendance::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'session_id' => $data['session_id'],
            ],
            [
                'status' => $data['status'],
                'waktu_scan' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => 'manual-admin',
            ],
        );

        return redirect()
            ->route('admin.reports.manual.form')
            ->with('status', 'Absensi manual berhasil disimpan.');
    }

    /**
     * Export rekap kelas ke Excel.
     */
    public function exportExcel(ClassRoom $class): BinaryFileResponse
    {
        return Excel::download(
            new ClassAttendanceExport($class),
            'rekap-'.$class->kode.'.xlsx',
        );
    }

    /**
     * Export rekap kelas ke PDF.
     */
    public function exportPdf(ClassRoom $class, AttendanceReport $report): Response
    {
        $class->load('students:id,nama,nis');

        $data = $report->forClass($class);

        $pdf = Pdf::loadView('admin.reports.pdf.class', [
            'class' => $class,
            'totalSesi' => $data['total_sesi'],
            'students' => $data['students'],
        ])->setPaper('a4', 'portrait');

        return $pdf->download('rekap-'.$class->kode.'.pdf');
    }
}
