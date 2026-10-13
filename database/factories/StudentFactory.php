<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Nama Indonesia tanpa gelar akademik (Faker id_ID::name() menyertakan
        // suffix gelar seperti "M.Kom.", sehingga kita susun manual).
        $nama = $this->namaIndonesia();

        $email = Str::slug($nama, '.').'.'.fake()->unique()->numerify('####').'@siswa.sch.id';

        return [
            'user_id' => User::factory()->siswa()->state([
                'name' => $nama,
                'email' => $email,
            ]),
            'nis' => fake()->unique()->numerify('23103#####'),
            'nama' => $nama,
            'phone' => fake()->unique()->numerify('08##########'),
        ];
    }

    /**
     * Susun nama depan + nama belakang Indonesia tanpa gelar.
     */
    private function namaIndonesia(): string
    {
        $isMale = fake()->boolean();

        $depan = $isMale
            ? fake()->firstNameMale()
            : fake()->firstNameFemale();

        $belakang = $isMale
            ? fake()->lastNameMale()
            : fake()->lastNameFemale();

        return trim($depan.' '.$belakang);
    }
}
