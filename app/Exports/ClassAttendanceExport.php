<?php

namespace App\Exports;

use App\Models\ClassRoom;
use App\Services\AttendanceReport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClassAttendanceExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(private ClassRoom $class) {}

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        $data = app(AttendanceReport::class)->forClass($this->class);

        return $data['students']->map(fn (array $row) => [
            $row['nis'],
            $row['nama'],
            $row['total_sesi'],
            $row['hadir'],
            $row['telat'],
            $row['izin'],
            $row['sakit'],
            $row['alpha'],
            $row['persen_hadir'].'%',
        ])->all();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['NIS', 'Nama', 'Total Sesi', 'Hadir', 'Telat', 'Izin', 'Sakit', 'Alpha', '% Hadir'];
    }

    public function title(): string
    {
        return 'Rekap '.$this->class->kode;
    }
}
