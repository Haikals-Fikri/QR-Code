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
     * Urutan untuk membuat jam sesi yang unik dalam satu kelas.
     */
    protected static int $jamSequence = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $schedule = Schedule::factory()->create();

        // Pastikan tiap sesi punya jam berbeda agar memenuhi
        // unique(class_id, tanggal, jam_mulai).
        $jamMulai = sprintf('%02d:00', 7 + (self::$jamSequence++ % 12));
        $jamSelesai = sprintf('%02d:59', 7 + (self::$jamSequence % 12));

        return [
            'schedule_id' => $schedule->id,
            'class_id' => $schedule->class_id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'toleransi' => $schedule->toleransi,
            'qr_token' => bin2hex(random_bytes(32)),
            'qr_token_aktif' => true,
        ];
    }
}
