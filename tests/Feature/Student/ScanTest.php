<?php

namespace Tests\Feature\Student;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper membuat siswa terdaftar di kelas dengan satu sesi.
     *
     * @return array{user: User, student: Student, class: ClassRoom, session: ScheduleSession}
     */
    private function makeEnrolledSession(string $mulai = '08:00', string $selesai = '10:00', int $toleransi = 15): array
    {
        $class = ClassRoom::factory()->create();
        $user = User::factory()->siswa()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $class->students()->attach($student->id);

        $schedule = Schedule::factory()->create([
            'class_id' => $class->id,
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'toleransi' => $toleransi,
            'tanggal_mulai' => today()->subDay(),
            'tanggal_selesai' => today()->addMonth(),
        ]);

        $session = ScheduleSession::factory()->create([
            'schedule_id' => $schedule->id,
            'class_id' => $class->id,
            'tanggal' => today()->toDateString(),
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'toleransi' => $toleransi,
        ]);

        return compact('user', 'student', 'class', 'session');
    }

    public function test_student_can_view_scan_page(): void
    {
        $data = $this->makeEnrolledSession();

        $this->actingAs($data['user'])
            ->get(route('scan.index'))
            ->assertOk()
            ->assertSee('Scan QR Absensi');
    }

    public function test_admin_cannot_access_scan_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('scan.index'))
            ->assertForbidden();
    }

    public function test_scan_marks_attendance_as_hadir(): void
    {
        Carbon::setTestNow(today()->setTime(8, 5)); // dalam toleransi

        $data = $this->makeEnrolledSession();

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'hadir']);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $data['student']->id,
            'session_id' => $data['session']->id,
            'status' => 'hadir',
        ]);

        Carbon::setTestNow();
    }

    public function test_scan_marks_attendance_as_telat(): void
    {
        Carbon::setTestNow(today()->setTime(8, 30)); // lewat toleransi 15 mnt

        $data = $this->makeEnrolledSession();

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'telat']);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $data['student']->id,
            'status' => 'telat',
        ]);

        Carbon::setTestNow();
    }

    public function test_scan_rejected_when_too_early(): void
    {
        Carbon::setTestNow(today()->setTime(7, 30)); // lebih awal dari 08:00 - 15 mnt

        $data = $this->makeEnrolledSession();

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('attendances', 0);

        Carbon::setTestNow();
    }

    public function test_scan_rejected_when_session_ended(): void
    {
        Carbon::setTestNow(today()->setTime(11, 0)); // setelah 10:00

        $data = $this->makeEnrolledSession();

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('attendances', 0);

        Carbon::setTestNow();
    }

    public function test_scan_rejected_for_unknown_token(): void
    {
        $data = $this->makeEnrolledSession();
        Carbon::setTestNow(today()->setTime(8, 5));

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => str_repeat('a', 64)])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        Carbon::setTestNow();
    }

    public function test_scan_rejected_when_student_not_enrolled(): void
    {
        Carbon::setTestNow(today()->setTime(8, 5));

        $data = $this->makeEnrolledSession();

        // Siswa lain yang tidak terdaftar di kelas.
        $outsider = User::factory()->siswa()->create();
        Student::factory()->create(['user_id' => $outsider->id]);

        $this->actingAs($outsider)
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('attendances', 0);

        Carbon::setTestNow();
    }

    public function test_duplicate_scan_is_rejected(): void
    {
        Carbon::setTestNow(today()->setTime(8, 5));

        $data = $this->makeEnrolledSession();

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertOk();

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertSame(1, Attendance::count());

        Carbon::setTestNow();
    }

    public function test_inactive_token_is_rejected(): void
    {
        Carbon::setTestNow(today()->setTime(8, 5));

        $data = $this->makeEnrolledSession();
        $data['session']->update(['qr_token_aktif' => false]);

        $this->actingAs($data['user'])
            ->postJson(route('scan.store'), ['token' => $data['session']->qr_token])
            ->assertStatus(422);

        Carbon::setTestNow();
    }
}
