<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\ScheduleSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScheduleSession>
 */
class ScheduleSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $schedule = Schedule::factory()->create();

        return [
            'schedule_id' => $schedule->id,
            'class_id' => $schedule->class_id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => $schedule->jam_mulai,
            'jam_selesai' => $schedule->jam_selesai,
            'toleransi' => $schedule->toleransi,
            'qr_token' => bin2hex(random_bytes(32)),
            'qr_token_aktif' => true,
        ];
    }
}
