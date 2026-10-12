<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateScheduleSessionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_sessions_for_matching_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-05')); // Senin

        $class = ClassRoom::factory()->create();
        Schedule::factory()->create([
            'class_id' => $class->id,
            'hari' => 'senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'toleransi' => 15,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-01-31',
        ]);

        $this->artisan('schedule:generate --days=14')->assertSuccessful();

        // 3 Senin dalam rentang 5-19 Jan (5, 12, 19).
        $this->assertSame(3, ScheduleSession::count());
        $this->assertDatabaseHas('schedule_sessions', [
            'class_id' => $class->id,
            'tanggal' => '2026-01-05',
        ]);

        Carbon::setTestNow();
    }

    public function test_does_not_duplicate_existing_sessions(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-05'));

        $class = ClassRoom::factory()->create();
        Schedule::factory()->create([
            'class_id' => $class->id,
            'hari' => 'senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-01-31',
        ]);

        $this->artisan('schedule:generate --days=14');
        $firstCount = ScheduleSession::count();

        $this->artisan('schedule:generate --days=14');

        $this->assertSame($firstCount, ScheduleSession::count());

        Carbon::setTestNow();
    }

    public function test_each_session_has_unique_qr_token(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-05'));

        ClassRoom::factory()->create();
        Schedule::factory()->create([
            'hari' => 'senin',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-01-31',
        ]);

        $this->artisan('schedule:generate --days=21');

        $tokens = ScheduleSession::pluck('qr_token')->all();
        $this->assertSame(count($tokens), count(array_unique($tokens)));
        $this->assertSame(64, strlen($tokens[0]));

        Carbon::setTestNow();
    }
}
