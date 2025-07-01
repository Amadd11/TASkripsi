<?php

namespace Database\Factories;

use App\Models\MasterPasien;
use App\Models\BenarReaksiObat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BenarReaksiObat>
 */
class BenarReaksiObatFactory extends Factory
{
    protected $model = BenarReaksiObat::class;

    public function definition(): array
    {
        $pasien = MasterPasien::inRandomOrder()->first();

        return [
            'no_cm' => $pasien->no_cm,
            'no_reg' => $pasien->no_reg,
            'jam' => $this->faker->time(),
            'id_petugas' => $this->faker->numberBetween(1, 5),
            'keterangan' => $this->faker->sentence(),
            'tanggal' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'is_alergi' => $this->faker->boolean(80), // 80% true
            'is_efek_samping' => $this->faker->boolean(80),
            'is_efek_terapi' => $this->faker->boolean(80),
        ];
    }
}
