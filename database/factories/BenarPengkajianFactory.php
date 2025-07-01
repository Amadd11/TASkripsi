<?php

namespace Database\Factories;

use App\Models\BenarPengkajian;
use App\Models\MasterPasien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BenarPengkajian>
 */
class BenarPengkajianFactory extends Factory
{
    protected $model = BenarPengkajian::class;

    public function definition(): array
    {
        $pasien = MasterPasien::inRandomOrder()->first();

        return [
            'no_cm' => $pasien->no_cm,
            'no_reg' => $pasien->no_reg,
            'tanggal' => $this->faker->dateTimeBetween('-2 months', 'now'),
            'jam' => $this->faker->time(),
            'id_petugas' => $this->faker->numberBetween(1, 5),
            'keterangan' => $this->faker->sentence(),
            'is_suhu' => $this->faker->boolean(80),
            'is_tensi' => $this->faker->boolean(80),
            'is_no_reg' => $this->faker->boolean(80),
        ];
    }
}
