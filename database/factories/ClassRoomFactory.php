<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassRoom>
 */
class ClassRoomFactory extends Factory
{
    /**
     * Daftar nama mata pelajaran (Indonesia) beserta kode kelasnya.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    private array $daftarKelas = [
        ['Pemrograman Web', 'PW'],
        ['Basis Data', 'BD'],
        ['Jaringan Komputer', 'JK'],
        ['Algoritma & Pemrograman', 'AP'],
        ['Pemrograman Mobile', 'PM'],
        ['Sistem Operasi', 'SO'],
        ['Rekayasa Perangkat Lunak', 'RPL'],
        ['Struktur Data', 'SD'],
        ['Kecerdasan Buatan', 'KB'],
        ['Keamanan Jaringan', 'KJ'],
    ];

    /**
     * Nomor urut untuk menjamin kode tetap unik antar banyak instance.
     */
    protected static int $sequence = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil berurutan (bukan acak) agar nama kelas tidak duplikat.
        [$nama, $kode] = $this->daftarKelas[self::$sequence % count($this->daftarKelas)];

        // Suffix nomor urut → kode selalu unik walau daftar kelas diulang.
        $urut = str_pad((string) (++self::$sequence), 2, '0', STR_PAD_LEFT);

        return [
            'nama' => $nama,
            'kode' => "{$kode}-{$urut}",
            'guru_id' => null,
        ];
    }
}
