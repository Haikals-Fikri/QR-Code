<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassRoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_class_list(): void
    {
        ClassRoom::factory()->create(['nama' => 'Pemrograman Web', 'kode' => 'PW-01']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.classes.index'))
            ->assertOk()
            ->assertSee('Pemrograman Web');
    }

    public function test_student_cannot_view_class_management(): void
    {
        $this->actingAs(User::factory()->siswa()->create())
            ->get(route('admin.classes.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_class(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.classes.store'), [
                'nama' => 'Basis Data',
                'kode' => 'BD-01',
            ])
            ->assertRedirect(route('admin.classes.index'));

        $this->assertDatabaseHas('classes', ['nama' => 'Basis Data', 'kode' => 'BD-01']);
    }

    public function test_class_code_must_be_unique(): void
    {
        ClassRoom::factory()->create(['kode' => 'BD-01']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.classes.store'), [
                'nama' => 'Basis Data Baru',
                'kode' => 'BD-01',
            ])
            ->assertSessionHasErrors('kode');
    }

    public function test_admin_can_update_class(): void
    {
        $class = ClassRoom::factory()->create(['nama' => 'Lama', 'kode' => 'OLD']);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.classes.update', $class), [
                'nama' => 'Baru',
                'kode' => 'OLD',
            ])
            ->assertRedirect(route('admin.classes.index'));

        $this->assertDatabaseHas('classes', ['id' => $class->id, 'nama' => 'Baru']);
    }

    public function test_admin_can_delete_class(): void
    {
        $class = ClassRoom::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.classes.destroy', $class))
            ->assertRedirect(route('admin.classes.index'));

        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
    }
}
