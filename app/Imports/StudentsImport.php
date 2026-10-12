<?php

namespace App\Imports;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements SkipsEmptyRows, SkipsOnFailure, ToCollection, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    /**
     * Jumlah baris berhasil diimport.
     */
    public int $importedCount = 0;

    /**
     * Kelas yang dianggap aktif (di-cache per kode).
     *
     * @var array<string, ClassRoom>
     */
    private array $classCache = [];

    /**
     * Proses setiap baris dari file yang diimport.
     *
     * @param  Collection<int, Collection<string, mixed>>  $rows
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            DB::transaction(function () use ($row) {
                $user = User::create([
                    'name' => $row['nama'],
                    'email' => $row['email'],
                    'password' => (string) $row['nis'],
                    'role' => 'siswa',
                ]);

                $student = Student::create([
                    'user_id' => $user->id,
                    'nis' => (string) $row['nis'],
                    'nama' => $row['nama'],
                    'phone' => $row['phone'] ?? null,
                ]);

                $classId = $this->resolveClassId($row['kode_kelas'] ?? null);

                if ($classId) {
                    $student->classes()->sync([$classId]);
                }

                $this->importedCount++;
            });
        }
    }

    /**
     * Aturan validasi tiap baris.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nis' => ['required', 'distinct', 'unique:students,nis'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'distinct', 'unique:users,email'],
            'phone' => ['nullable'],
            'kode_kelas' => ['nullable', 'exists:classes,kode'],
        ];
    }

    /**
     * Pesan validasi kustom (Indonesia).
     *
     * @return array<string, string>
     */
    public function customValidationMessages(): array
    {
        return [
            'nis.required' => 'Kolom NIS wajib diisi (baris :row).',
            'nis.unique' => 'NIS :input sudah terdaftar (baris :row).',
            'nis.distinct' => 'NIS :input duplikat di dalam file (baris :row).',
            'nama.required' => 'Kolom nama wajib diisi (baris :row).',
            'email.required' => 'Kolom email wajib diisi (baris :row).',
            'email.email' => 'Format email :input tidak valid (baris :row).',
            'email.unique' => 'Email :input sudah terdaftar (baris :row).',
            'kode_kelas.exists' => 'Kode kelas :input tidak ditemukan (baris :row).',
        ];
    }

    /**
     * Cari ID kelas berdasarkan kode, dengan cache.
     */
    private function resolveClassId(?string $kode): ?int
    {
        if (blank($kode)) {
            return null;
        }

        $kode = trim($kode);

        if (! isset($this->classCache[$kode])) {
            $class = ClassRoom::where('kode', $kode)->first();

            if ($class) {
                $this->classCache[$kode] = $class;
            } else {
                return null;
            }
        }

        return $this->classCache[$kode]->id;
    }
}
