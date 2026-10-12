<?php

namespace App\Models;

use Database\Factories\ClassRoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kode', 'guru_id'])]
class ClassRoom extends Model
{
    /** @use HasFactory<ClassRoomFactory> */
    use HasFactory;

    /**
     * Nama tabel yang digunakan model ini.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * Guru/pengajar kelas ini.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Siswa-siswa yang terdaftar di kelas ini.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')
            ->withTimestamps();
    }

    /**
     * Pola jadwal kelas ini.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    /**
     * Sesi-sesi kelas ini.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(ScheduleSession::class, 'class_id');
    }
}
