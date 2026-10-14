<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentLoginPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_buatan_factory_bisa_login_dengan_nis(): void
    {
        $student = Student::factory()->create();
        $user = $student->user;

        // Password awal siswa = NIS (sesuai desain sistem).
        $this->assertTrue(
            Hash::check((string) $student->nis, $user->password),
            'Password siswa hasil factory harus sama dengan NIS-nya.',
        );

        $this->post('/login', [
            'email' => $user->email,
            'password' => (string) $student->nis,
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_siswa_tidak_bisa_login_dengan_password_salah(): void
    {
        $student = Student::factory()->create();

        $this->post('/login', [
            'email' => $student->user->email,
            'password' => 'password-yang-salah',
        ])->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_seeder_membuat_akun_demo_yang_bisa_login(): void
    {
        $this->seed();

        // Admin demo: password "password".
        $admin = User::where('email', 'admin@qrcode.test')->first();
        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('password', $admin->password));

        // Siswa demo: password "password".
        $siswa = User::where('email', 'siswa@qrcode.test')->first();
        $this->assertNotNull($siswa);
        $this->assertTrue(Hash::check('password', $siswa->password));

        // Siswa dummy hasil seeder: password = NIS.
        $dummy = Student::with('user')
            ->whereHas('user', fn ($q) => $q->where('email', '!=', 'siswa@qrcode.test'))
            ->first();

        $this->assertNotNull($dummy);
        $this->assertTrue(
            Hash::check((string) $dummy->nis, $dummy->user->password),
            'Password siswa hasil seeder harus sama dengan NIS-nya.',
        );
    }
}
