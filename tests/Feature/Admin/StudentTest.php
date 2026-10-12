<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_student_list(): void
    {
        $student = Student::factory()->create(['nama' => 'Budi Santoso']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('Budi Santoso');
    }

    public function test_student_cannot_view_student_management(): void
    {
        $this->actingAs(User::factory()->siswa()->create())
            ->get(route('admin.students.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_student_with_generated_account(): void
    {
        $class = ClassRoom::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.students.store'), [
                'nis' => '231039999',
                'nama' => 'Siti Aminah',
                'email' => 'siti@qrcode.test',
                'classes' => [$class->id],
            ])
            ->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'siti@qrcode.test',
            'role' => 'siswa',
            'name' => 'Siti Aminah',
        ]);

        $this->assertDatabaseHas('students', [
            'nis' => '231039999',
            'nama' => 'Siti Aminah',
        ]);

        $student = Student::where('nis', '231039999')->first();
        $this->assertTrue($student->classes->contains($class->id));
    }

    public function test_duplicate_nis_is_rejected(): void
    {
        Student::factory()->create(['nis' => '231039999']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.students.store'), [
                'nis' => '231039999',
                'nama' => 'Duplikat',
                'email' => 'dup@qrcode.test',
            ])
            ->assertSessionHasErrors('nis');
    }

    public function test_admin_can_update_student(): void
    {
        $student = Student::factory()->create(['nama' => 'Nama Lama']);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.students.update', $student), [
                'nis' => $student->nis,
                'nama' => 'Nama Baru',
                'email' => $student->user->email,
            ])
            ->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('students', ['id' => $student->id, 'nama' => 'Nama Baru']);
    }

    public function test_admin_can_delete_student_and_account(): void
    {
        $student = Student::factory()->create();
        $userId = $student->user_id;

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.students.destroy', $student))
            ->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }
}
