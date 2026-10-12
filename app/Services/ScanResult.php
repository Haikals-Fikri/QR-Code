<?php

namespace App\Services;

use App\Models\Attendance;

/**
 * Hasil pemindaian absensi.
 */
class ScanResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?Attendance $attendance = null,
    ) {}

    /**
     * Buat hasil sukses.
     */
    public static function success(string $message, ?Attendance $attendance = null): self
    {
        return new self(true, $message, $attendance);
    }

    /**
     * Buat hasil gagal.
     */
    public static function failure(string $message): self
    {
        return new self(false, $message);
    }
}
