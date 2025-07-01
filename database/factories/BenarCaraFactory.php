<?php

namespace Database\Factories;

use App\Models\BenarCara;
use App\Models\MasterPasien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BenarCara>
 */
class BenarCaraFactory extends Factory
{
    protected $model = BenarCara::class;

    public function definition(): array
    {
        $pasien = MasterPasien::inRandomOrder()->first();


        return [
            'no_cm' => $pasien->no_cm,
            'no_reg' => $pasien->no_reg,
            'tanggal' => $this->faker->dateTimeBetween('-2 months', 'now'),
            'is_oral' => $this->faker->boolean(80),
            'is_im' => $this->faker->boolean(80),
            'is_iv' => $this->faker->boolean(80),
            'is_no_reg' => $this->faker->boolean(80),
            'jam' => $this->faker->time(),
            'id_petugas' => $this->faker->numberBetween(1, 5),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
