<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: kelas dengan 2 sesi dan 1 siswa dengan status tertentu.
     */
    private function makeClassWithData(): array
    {
        $class = ClassRoom::factory()->create(['kode' => 'PW-01', 'nama' => 'Pemrograman Web']);
        $student = Student::factory()->create(['nama' => 'Budi Santoso', 'nis' => '231031001']);
        $class->students()->attach($student->id);

        $schedule = Schedule::factory()->create(['class_id' => $class->id]);

        $session1 = ScheduleSession::factory()->create([
            'schedule_id' => $schedule->id,
            'class_id' => $class->id,
        ]);
        $session2 = ScheduleSession::factory()->create([
            'schedule_id' => $schedule->id,
            'class_id' => $class->id,
        ]);

        Attendance::factory()->create([
            'student_id' => $student->id,
            'session_id' => $session1->id,
            'status' => 'hadir',
        ]);
        Attendance::factory()->create([
            'student_id' => $student->id,
            'session_id' => $session2->id,
            'status' => 'telat',
        ]);

        return compact('class', 'student');
    }

    public function test_summarize_computes_alpha(): void
    {
        $data = $this->makeClassWithData();

        $summary = app(AttendanceReport::class)->summarize($data['student']);

        // 2 sesi, 1 hadir, 1 telat → alpha 0.
        $this->assertSame(2, $summary['total_sesi']);
        $this->assertSame(1, $summary['hadir']);
        $this->assertSame(1, $summary['telat']);
        $this->assertSame(0, $summary['alpha']);
        $this->assertSame(100.0, $summary['persen_hadir']);
    }

    public function test_alpha_counted_for_missing_session(): void
    {
        $class = ClassRoom::factory()->create();
        $student = Student::factory()->create();
        $class->students()->attach($student->id);

        $schedule = Schedule::factory()->create(['class_id' => $class->id]);
        $sessions = ScheduleSession::factory()->count(3)->create([
            'schedule_id' => $schedule->id,
            'class_id' => $class->id,
        ]);

        // Hanya 1 dari 3 sesi yang tercatat hadir → alpha 2.
        Attendance::factory()->create([
            'student_id' => $student->id,
            'session_id' => $sessions->first()->id,
            'status' => 'hadir',
        ]);

        $summary = app(AttendanceReport::class)->summarize($student);

        $this->assertSame(3, $summary['total_sesi']);
        $this->assertSame(1, $summary['hadir']);
        $this->assertSame(2, $summary['alpha']);
    }

    public function test_admin_can_view_reports_index(): void
    {
        ClassRoom::factory()->create(['nama' => 'Pemrograman Web']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Pemrograman Web');
    }

    public function test_admin_can_view_class_report(): void
    {
        $data = $this->makeClassWithData();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.class', $data['class']))
            ->assertOk()
            ->assertSee('Budi Santoso');
    }

    public function test_admin_can_view_student_report(): void
    {
        $data = $this->makeClassWithData();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.student', $data['student']))
            ->assertOk()
            ->assertSee('Budi Santoso');
    }

    public function test_student_cannot_view_reports(): void
    {
        $this->actingAs(User::factory()->siswa()->create())
            ->get(route('admin.reports.index'))
            ->assertForbidden();
    }

    public function test_admin_can_export_class_excel(): void
    {
        $data = $this->makeClassWithData();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.class.excel', $data['class']))
            ->assertOk()
            ->assertDownload('rekap-PW-01.xlsx');
    }

    public function test_admin_can_export_class_pdf(): void
    {
        $data = $this->makeClassWithData();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.class.pdf', $data['class']))
            ->assertOk()
            ->assertDownload('rekap-PW-01.pdf');
    }

    public function test_admin_can_create_manual_attendance(): void
    {
        $data = $this->makeClassWithData();
        $session = ScheduleSession::where('class_id', $data['class']->id)->first();

        // Siswa lain tanpa absensi.
        $other = Student::factory()->create();
        $data['class']->students()->attach($other->id);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.reports.manual.store'), [
                'student_id' => $other->id,
                'session_id' => $session->id,
                'status' => 'izin',
            ])
            ->assertRedirect(route('admin.reports.manual.form'));

        $this->assertDatabaseHas('attendances', [
            'student_id' => $other->id,
            'session_id' => $session->id,
            'status' => 'izin',
        ]);
    }

    public function test_manual_attendance_overwrites_existing(): void
    {
        $data = $this->makeClassWithData();
        $attendance = Attendance::where('student_id', $data['student']->id)->first();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.reports.manual.store'), [
                'student_id' => $data['student']->id,
                'session_id' => $attendance->session_id,
                'status' => 'sakit',
            ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'sakit',
        ]);
        $this->assertSame(2, Attendance::where('student_id', $data['student']->id)->count());
    }
}
