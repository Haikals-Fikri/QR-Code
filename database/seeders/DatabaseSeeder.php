<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ScheduleSession;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin default.
        User::factory()->admin()->create([
            'name' => 'Administrator',
            'email' => 'admin@qrcode.test',
            'password' => Hash::make('password'),
        ]);

        // Beberapa kelas dengan siswa terdaftar.
        $classes = ClassRoom::factory(3)->create();

        $classes->each(function (ClassRoom $class) {
            $students = Student::factory(10)->create();

            $class->students()->attach($students->pluck('id'));

            $schedule = $class->schedules()->create([
                'hari' => 'senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'toleransi' => 15,
                'tanggal_mulai' => now()->startOfMonth(),
                'tanggal_selesai' => now()->addMonths(3)->endOfMonth(),
            ]);

            // Contoh sesi untuk hari ini beserta beberapa absensi.
            $session = ScheduleSession::factory()->create([
                'schedule_id' => $schedule->id,
                'class_id' => $class->id,
                'tanggal' => now()->toDateString(),
            ]);

            $students->take(5)->each(function (Student $student) use ($session) {
                Attendance::factory()->create([
                    'student_id' => $student->id,
                    'session_id' => $session->id,
                ]);
            });
        });

        // Akun siswa contoh untuk login manual.
        $demoUser = User::factory()->siswa()->create([
            'name' => 'Siswa Demo',
            'email' => 'siswa@qrcode.test',
            'password' => Hash::make('password'),
        ]);

        Student::factory()->create([
            'user_id' => $demoUser->id,
            'nis' => '231031016',
            'nama' => 'Siswa Demo',
        ]);
    }
}
