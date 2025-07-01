<?php

namespace Database\Factories;

use App\Models\MasterPasien;
use App\Models\BenarDokumentasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BenarDokumentasi>
 */
class BenarDokumentasiFactory extends Factory
{
    protected $model = BenarDokumentasi::class;

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
            'is_no_reg' => $this->faker->boolean(80),
            'is_pasien' => $this->faker->boolean(80),
            'is_dosis' => $this->faker->boolean(80),
            'is_obat' => $this->faker->boolean(80),
            'is_waktu' => $this->faker->boolean(80),
            'is_rute' => $this->faker->boolean(80),
        ];
    }
}
