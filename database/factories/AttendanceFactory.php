<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\ScheduleSession;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'session_id' => ScheduleSession::factory(),
            'waktu_scan' => now(),
            'status' => fake()->randomElement(['hadir', 'telat', 'izin', 'sakit']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
