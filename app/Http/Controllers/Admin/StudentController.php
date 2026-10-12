<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar siswa.
     */
    public function index(): View
    {
        $students = Student::query()
            ->with(['user:id,name,email', 'classes:id,nama,kode'])
            ->orderBy('nama')
            ->get();

        return view('admin.students.index', compact('students'));
    }

    /**
     * Tampilkan form tambah siswa.
     */
    public function create(): View
    {
        return view('admin.students.create', [
            'classes' => $this->classOptions(),
        ]);
    }

    /**
     * Simpan siswa baru beserta akun login-nya.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();

            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => $data['nis'],
                'role' => 'siswa',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'nis' => $data['nis'],
                'nama' => $data['nama'],
                'phone' => $data['phone'] ?? null,
            ]);

            $student->classes()->sync($data['classes'] ?? []);
        });

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Siswa berhasil ditambahkan. Password awal = NIS.');
    }

    /**
     * Tampilkan form edit siswa.
     */
    public function edit(Student $student): View
    {
        $student->load(['user', 'classes']);

        return view('admin.students.edit', [
            'student' => $student,
            'classes' => $this->classOptions(),
        ]);
    }

    /**
     * Perbarui siswa.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        DB::transaction(function () use ($request, $student) {
            $data = $request->validated();

            $student->update([
                'nis' => $data['nis'],
                'nama' => $data['nama'],
                'phone' => $data['phone'] ?? null,
            ]);

            $student->user->update([
                'name' => $data['nama'],
                'email' => $data['email'],
            ]);

            $student->classes()->sync($data['classes'] ?? []);
        });

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa beserta akun login-nya.
     */
    public function destroy(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student) {
            $user = $student->user;
            $student->delete();
            $user?->delete();
        });

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Siswa berhasil dihapus.');
    }

    /**
     * Opsi kelas untuk pilihan multi-checkbox.
     *
     * @return Collection<int, ClassRoom>
     */
    private function classOptions(): Collection
    {
        return ClassRoom::query()->orderBy('nama')->get();
    }
}
