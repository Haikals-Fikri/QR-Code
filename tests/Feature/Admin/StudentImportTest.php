<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buat file CSV sementara berisi baris yang diberikan.
     *
     * @param  array<int, array<int, string>>  $rows
     */
    private function makeCsv(array $rows): UploadedFile
    {
        $header = ['nis', 'nama', 'email', 'phone', 'kode_kelas'];

        $path = tempnam(sys_get_temp_dir(), 'import').'.csv';
        $handle = fopen($path, 'w');
        fputcsv($handle, $header);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return new UploadedFile($path, 'siswa.csv', 'text/csv', null, true);
    }

    public function test_admin_can_view_import_form(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.students.import.create'))
            ->assertOk()
            ->assertSee('Import Siswa');
    }

    public function test_student_cannot_access_import_form(): void
    {
        $this->actingAs(User::factory()->siswa()->create())
            ->get(route('admin.students.import.create'))
            ->assertForbidden();
    }

    public function test_admin_can_import_students_with_class(): void
    {
        $class = ClassRoom::factory()->create(['kode' => 'PW-01', 'nama' => 'Pemrograman Web']);

        $file = $this->makeCsv([
            ['231031001', 'Ahmad Fauzi', 'ahmad@example.com', '081234567890', 'PW-01'],
            ['231031002', 'Siti Aminah', 'siti@example.com', '', 'PW-01'],
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.students.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('students', ['nis' => '231031001', 'nama' => 'Ahmad Fauzi']);
        $this->assertDatabaseHas('users', ['email' => 'ahmad@example.com', 'role' => 'siswa']);

        $student = Student::where('nis', '231031001')->first();
        $this->assertTrue($student->classes->contains($class->id));
    }

    public function test_import_rejects_existing_nis(): void
    {
        Student::factory()->create(['nis' => '231031001']);
        ClassRoom::factory()->create(['kode' => 'PW-01']);

        $file = $this->makeCsv([
            ['231031001', 'Duplikat', 'dup@example.com', '', 'PW-01'],
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.students.import.store'), ['file' => $file]);

        // Diarahkan kembali ke form karena ada baris gagal.
        $response->assertRedirect(route('admin.students.import.create'));
        $this->assertDatabaseMissing('users', ['email' => 'dup@example.com']);
    }

    public function test_import_rejects_unknown_class_code(): void
    {
        $file = $this->makeCsv([
            ['231031099', 'Tanpa Kelas', 'noclass@example.com', '', 'TIDAK-ADA'],
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.students.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.students.import.create'));

        $this->assertDatabaseMissing('users', ['email' => 'noclass@example.com']);
    }

    public function test_admin_can_download_template(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.students.import.template'))
            ->assertOk()
            ->assertDownload('template-import-siswa.xlsx');
    }

    public function test_partial_import_keeps_valid_rows(): void
    {
        ClassRoom::factory()->create(['kode' => 'PW-01']);
        Student::factory()->create(['nis' => '231031000']);

        $file = $this->makeCsv([
            ['231031001', 'Valid Satu', 'valid1@example.com', '', 'PW-01'],
            ['231031000', 'NIS Duplikat', 'dup@example.com', '', 'PW-01'],
            ['231031002', 'Valid Dua', 'valid2@example.com', '', 'PW-01'],
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.students.import.store'), ['file' => $file])
            ->assertRedirect(route('admin.students.import.create'));

        // Baris valid tetap masuk, baris gagal tidak.
        $this->assertDatabaseHas('students', ['nis' => '231031001']);
        $this->assertDatabaseHas('students', ['nis' => '231031002']);
        $this->assertDatabaseMissing('users', ['email' => 'dup@example.com']);
    }
}
