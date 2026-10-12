<?php

namespace App\Models;

use Database\Factories\ScheduleSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['schedule_id', 'class_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'toleransi', 'qr_token', 'qr_token_aktif'])]
class ScheduleSession extends Model
{
    /** @use HasFactory<ScheduleSessionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'qr_token_aktif' => 'boolean',
        ];
    }

    /**
     * Pola jadwal sumber sesi ini.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Kelas dari sesi ini.
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Catatan absensi pada sesi ini.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }
}
