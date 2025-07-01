<?php

namespace Database\Factories;

use App\Models\BenarHakClient;
use App\Models\MasterPasien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BenarHakClient>
 */
class BenarHakClientFactory extends Factory
{
    protected $model = BenarHakClient::class;

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
            'is_ic' => $this->faker->boolean(80),
            'is_no_reg' => $this->faker->boolean(80),
        ];
    }
}
