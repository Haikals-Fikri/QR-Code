<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentTemplateExport implements FromArray, WithHeadings
{
    /**
     * Data contoh baris pertama.
     *
     * @return array<int, array<int, string>>
     */
    public function array(): array
    {
        return [
            ['231031001', 'Ahmad Fauzi', 'ahmad@example.com', '081234567890', 'PW-01'],
        ];
    }

    /**
     * Judul kolom template.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['nis', 'nama', 'email', 'phone', 'kode_kelas'];
    }
}
