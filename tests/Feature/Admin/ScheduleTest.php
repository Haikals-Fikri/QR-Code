<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_schedule_list(): void
    {
        $class = ClassRoom::factory()->create(['nama' => 'Pemrograman Web']);
        Schedule::factory()->create(['class_id' => $class->id, 'hari' => 'senin']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.schedules.index'))
            ->assertOk()
            ->assertSee('Pemrograman Web');
    }

    public function test_student_cannot_view_schedule_management(): void
    {
        $this->actingAs(User::factory()->siswa()->create())
            ->get(route('admin.schedules.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_schedule(): void
    {
        $class = ClassRoom::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.schedules.store'), [
                'class_id' => $class->id,
                'hari' => 'rabu',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'toleransi' => 15,
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-03-31',
            ])
            ->assertRedirect(route('admin.schedules.index'));

        $this->assertDatabaseHas('schedules', ['class_id' => $class->id, 'hari' => 'rabu']);
    }

    public function test_jam_selesai_must_be_after_jam_mulai(): void
    {
        $class = ClassRoom::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.schedules.store'), [
                'class_id' => $class->id,
                'hari' => 'rabu',
                'jam_mulai' => '10:00',
                'jam_selesai' => '08:00',
                'toleransi' => 15,
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-03-31',
            ])
            ->assertSessionHasErrors('jam_selesai');
    }

    public function test_admin_can_view_session_qr(): void
    {
        $session = ScheduleSession::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.sessions.show', $session))
            ->assertOk()
            ->assertSee('QR Absensi');
    }

    public function test_admin_can_view_printable_qr(): void
    {
        $session = ScheduleSession::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.sessions.print', $session))
            ->assertOk()
            ->assertSee('Pindai QR ini');
    }

    public function test_admin_can_regenerate_token(): void
    {
        $session = ScheduleSession::factory()->create();
        $oldToken = $session->qr_token;

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.sessions.regenerate', $session));

        $this->assertNotSame($oldToken, $session->fresh()->qr_token);
    }

    public function test_admin_can_manually_generate_sessions(): void
    {
        $class = ClassRoom::factory()->create();
        $schedule = Schedule::factory()->create([
            'class_id' => $class->id,
            'tanggal_mulai' => now()->subDay(),
            'tanggal_selesai' => now()->addMonth(),
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.schedules.generate', $schedule))
            ->assertRedirect(route('admin.schedules.sessions', $schedule));

        $this->assertGreaterThan(0, ScheduleSession::where('schedule_id', $schedule->id)->count());
    }
}
