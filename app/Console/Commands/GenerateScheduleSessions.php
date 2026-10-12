<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\ScheduleSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateScheduleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:generate
                            {--days=30 : Jumlah hari ke depan yang akan di-generate}
                            {--schedule= : ID pola jadwal tertentu (kosong = semua)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sesi kelas (schedule_sessions) beserta QR token dari pola jadwal berulang';

    /**
     * Peta nama hari Indonesia ke angka Carbon (0=Minggu ... 6=Sabtu).
     *
     * @var array<string, int>
     */
    private array $hariMap = [
        'senin' => 1,
        'selasa' => 2,
        'rabu' => 3,
        'kamis' => 4,
        'jumat' => 5,
        'sabtu' => 6,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $from = Carbon::today();
        $to = Carbon::today()->addDays($days);

        $query = Schedule::query()->with('classRoom');

        if ($scheduleId = $this->option('schedule')) {
            $query->whereKey($scheduleId);
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            $this->warn('Tidak ada pola jadwal yang ditemukan.');

            return self::SUCCESS;
        }

        $totalCreated = 0;

        foreach ($schedules as $schedule) {
            $totalCreated += $this->generateForSchedule($schedule, $from, $to);
        }

        $this->info("Selesai. {$totalCreated} sesi baru dibuat.");

        return self::SUCCESS;
    }

    /**
     * Generate sesi untuk satu pola jadwal.
     *
     * Batas tanggal mengikuti min(rentang pola, rentang hari ke depan).
     */
    private function generateForSchedule(Schedule $schedule, Carbon $from, Carbon $to): int
    {
        $weekday = $this->hariMap[$schedule->hari] ?? null;

        if ($weekday === null) {
            return 0;
        }

        $start = $from->copy();
        $end = $to->copy();

        // Batasi dengan rentang berlaku pola jadwal.
        if ($schedule->tanggal_mulai && $schedule->tanggal_mulai->greaterThan($start)) {
            $start = $schedule->tanggal_mulai->copy();
        }

        if ($schedule->tanggal_selesai && $schedule->tanggal_selesai->lessThan($end)) {
            $end = $schedule->tanggal_selesai->copy();
        }

        if ($start->greaterThan($end)) {
            return 0;
        }

        $created = 0;
        $cursor = $start->copy();

        // Majukan ke hari pertama yang cocok.
        while ($cursor->dayOfWeek !== $weekday) {
            $cursor->addDay();
        }

        while ($cursor->lessThanOrEqualTo($end)) {
            $exists = ScheduleSession::query()
                ->where('class_id', $schedule->class_id)
                ->where('tanggal', $cursor->toDateString())
                ->where('jam_mulai', $schedule->jam_mulai)
                ->exists();

            if (! $exists) {
                ScheduleSession::create([
                    'schedule_id' => $schedule->id,
                    'class_id' => $schedule->class_id,
                    'tanggal' => $cursor->toDateString(),
                    'jam_mulai' => $schedule->jam_mulai,
                    'jam_selesai' => $schedule->jam_selesai,
                    'toleransi' => $schedule->toleransi,
                    'qr_token' => bin2hex(random_bytes(32)),
                    'qr_token_aktif' => true,
                ]);

                $created++;
            }

            $cursor->addWeek();
        }

        return $created;
    }
}
