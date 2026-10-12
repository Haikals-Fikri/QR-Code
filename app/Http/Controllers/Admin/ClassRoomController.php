<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassRoomRequest;
use App\Http\Requests\Admin\UpdateClassRoomRequest;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ClassRoomController extends Controller
{
    /**
     * Tampilkan daftar kelas.
     */
    public function index(): View
    {
        $classes = ClassRoom::query()
            ->with('guru:id,name')
            ->withCount('students')
            ->orderBy('nama')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Tampilkan form tambah kelas.
     */
    public function create(): View
    {
        return view('admin.classes.create', [
            'gurus' => $this->guruOptions(),
        ]);
    }

    /**
     * Simpan kelas baru.
     */
    public function store(StoreClassRoomRequest $request): RedirectResponse
    {
        ClassRoom::create($request->validated());

        return redirect()
            ->route('admin.classes.index')
            ->with('status', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit kelas.
     */
    public function edit(ClassRoom $classRoom): View
    {
        return view('admin.classes.edit', [
            'classRoom' => $classRoom,
            'gurus' => $this->guruOptions(),
        ]);
    }

    /**
     * Perbarui kelas.
     */
    public function update(UpdateClassRoomRequest $request, ClassRoom $classRoom): RedirectResponse
    {
        $classRoom->update($request->validated());

        return redirect()
            ->route('admin.classes.index')
            ->with('status', 'Kelas berhasil diperbarui.');
    }

    /**
     * Hapus kelas.
     */
    public function destroy(ClassRoom $classRoom): RedirectResponse
    {
        $classRoom->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('status', 'Kelas berhasil dihapus.');
    }

    /**
     * Opsi guru (user admin) untuk dropdown pengajar.
     *
     * @return Collection<int, string>
     */
    private function guruOptions(): Collection
    {
        return User::query()
            ->where('role', 'admin')
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
