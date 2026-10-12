<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleRequest;
use App\Http\Requests\Admin\UpdateScheduleRequest;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Tampilkan daftar pola jadwal.
     */
    public function index(): View
    {
        $schedules = Schedule::query()
            ->with('classRoom:id,nama,kode')
            ->withCount('sessions')
            ->latest()
            ->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Tampilkan form tambah pola jadwal.
     */
    public function create(): View
    {
        return view('admin.schedules.create', [
            'classes' => $this->classOptions(),
        ]);
    }

    /**
     * Simpan pola jadwal baru.
     */
    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        Schedule::create($request->validated());

        return redirect()
            ->route('admin.schedules.index')
            ->with('status', 'Jadwal berhasil ditambahkan. Generate sesi untuk membuat QR.');
    }

    /**
     * Tampilkan form edit pola jadwal.
     */
    public function edit(Schedule $schedule): View
    {
        return view('admin.schedules.edit', [
            'schedule' => $schedule,
            'classes' => $this->classOptions(),
        ]);
    }

    /**
     * Perbarui pola jadwal.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()
            ->route('admin.schedules.index')
            ->with('status', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Hapus pola jadwal beserta sesinya.
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()
            ->route('admin.schedules.index')
            ->with('status', 'Jadwal berhasil dihapus.');
    }

    /**
     * Tampilkan daftar sesi dari sebuah pola jadwal.
     */
    public function sessions(Schedule $schedule): View
    {
        $schedule->load('classRoom:id,nama,kode');

        $sessions = ScheduleSession::query()
            ->where('schedule_id', $schedule->id)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.schedules.sessions', compact('schedule', 'sessions'));
    }

    /**
     * Opsi kelas untuk dropdown.
     *
     * @return Collection<int, ClassRoom>
     */
    private function classOptions(): Collection
    {
        return ClassRoom::query()->orderBy('nama')->get();
    }
}
