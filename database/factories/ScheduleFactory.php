<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hari = fake()->randomElement(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
        $jamMulai = fake()->randomElement(['07:00', '08:00', '10:00', '13:00']);

        return [
            'class_id' => ClassRoom::factory(),
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => date('H:i', strtotime($jamMulai.' +2 hours')),
            'toleransi' => 15,
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->addMonths(3)->endOfMonth(),
        ];
    }
}
