<?php

namespace Database\Factories;

use App\Models\FarTransaction; // Pastikan ini sesuai dengan namespace model FarTransaction Anda
use App\Models\MasterPasien;   // Import model MasterPasien
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FarTransaction>
 */
class FarTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FarTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $pasienData = MasterPasien::inRandomOrder()->first();

        $biaya = $this->faker->numberBetween(50000, 500000);
        $subEmbalase = $this->faker->numberBetween(0, 20000);
        $subRacikan = $this->faker->numberBetween(0, 70000);
        $grandTotal = $biaya + $subEmbalase + $subRacikan;

        return [
            'no_cm' => $pasienData['no_cm'],
            'tgl' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'jam' => $this->faker->time('H:i:s'),
            'unit' => Str::limit($this->faker->randomElement(['Farmasi', 'Gizi', 'Fisioterapi', 'Poli Umum']), 15, ''), // Disesuaikan: max 15
            'dokter' => Str::limit($this->faker->name('male'), 30, ''), // Disesuaikan: max 30
            'petugas' => Str::limit($this->faker->name('female'), 30, ''), // Disesuaikan: max 30
            'sampel' => Str::limit($this->faker->randomElement(['Ada', 'Tidak Ada', 'Rusak']), 30, ''), // Disesuaikan: max 30
            'no_reg' => $pasienData['no_reg'], // Dari MasterPasien, diasumsikan sudah benar (max 8)
            'pengirim' => Str::limit($this->faker->randomElement(['Poli Umum', 'UGD', 'Rawat Inap', 'Dokter Spesialis']), 30, ''), // Disesuaikan: max 30
            'biaya' => $biaya,
            'cetak' => Str::limit($this->faker->randomElement(['Sudah', 'Belum']), 5, ''), // Disesuaikan: max 5
            'lunas' => Str::limit($this->faker->randomElement(['LUNAS', 'BELUM',]), 5, ''), // Disesuaikan: max 5
            'tgl_lahir' => Carbon::parse($pasienData['tgl_lahir']),
            'alamat' => Str::limit($pasienData['alamat'], 50, ''), // Disesuaikan: max 50
            'sex' => $pasienData['sex'], // Dari MasterPasien, diasumsikan sudah benar (max 1)
            'kelas' => $pasienData['kelas'], // Dari MasterPasien, diasumsikan sudah benar (max 5)
            'nama_pas' => Str::limit($pasienData['nama_pas'], 30, ''), // Disesuaikan: max 30
            'iol' => $pasienData['iol'], // Dari MasterPasien, diasumsikan sudah benar (max 1)
            'rujuk' => Str::limit($this->faker->randomElement(['Ya', 'Tidak', 'Internal']), 20, ''), // Disesuaikan: max 20
            'bl_kunj' => $this->faker->randomLetter(), // Max 1
            'shift' => $this->faker->randomElement(['P', 'S', 'M']), // Pagi, Siang, Malam (max 1)
            'no_psn' => $this->faker->randomNumber(3, true), // 3 digit angka, integer
            'asuransi' => Str::limit($this->faker->randomElement(['BPJS Kesehatan', 'Asuransi Swasta A', 'Umum']), 30, ''), // Disesuaikan: max 30
            'biaya_pns' => $this->faker->numberBetween(0, 100000),
            'tgl_ambil' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d'), // Tipe Date
            'jam_ambil' => $this->faker->time('H:i:s'), // Tipe Time
            'menit' => $this->faker->numberBetween(10, 120),
            'panggil' => $this->faker->boolean(), // Integer (0/1)
            'racikan' => $subRacikan,
            'bpjs' => $this->faker->numberBetween(0, 200000),
            'catatan' => Str::limit($this->faker->sentence(5), 200, ''), // Disesuaikan: max 200
            'loket' => $this->faker->numberBetween(1, 5),
            'sub_embalase' => $subEmbalase,
            'sub_er' => $this->faker->numberBetween(0, 10000),
            'sub_racikan' => $subRacikan,
            'sub_item_er' => $this->faker->numberBetween(0, 5000),
            'grand_total' => $grandTotal,
            'bayar' => $grandTotal,
            'emr' => $this->faker->boolean(), // Integer (0/1)
            'bagian' => $this->faker->numberBetween(0, 10000),
            'gp' => $this->faker->numberBetween(0, 5000),
            'id_h_cp' => $this->faker->randomNumber(1, false), // 0 atau 1
            'klinik_online' => $this->faker->boolean(), // Integer (0/1)
            'f_terapi_plg' => $this->faker->boolean(), // Integer (0/1)
            'no_tunggu' => $this->faker->randomNumber(2, true), // 2 digit angka, integer
            'vlag_saji' => $this->faker->boolean(), // Boolean (0/1)
            'tanggal_saji' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'), // Tipe Date
            'jam_saji' => $this->faker->time('H:i:s'), // Tipe char(8)
            'tr_Jelas' => $this->faker->boolean(), // Boolean (0/1)
            'tr_obat' => $this->faker->boolean(), // Boolean (0/1)
            'tr_dosis' => $this->faker->boolean(), // Boolean (0/1)
            'tr_rute' => $this->faker->boolean(), // Boolean (0/1)
            'tr_waktu' => $this->faker->boolean(), // Boolean (0/1)
            'tr_duplikasi' => $this->faker->boolean(), // Boolean (0/1)
            'tr_interaksi' => $this->faker->boolean(), // Boolean (0/1)
            'tr_kontradiksi' => $this->faker->boolean(), // Boolean (0/1)
            'to_identitas' => $this->faker->boolean(), // Boolean (0/1)
            'to_obat' => $this->faker->boolean(), // Boolean (0/1)
            'to_jumlah' => $this->faker->boolean(), // Boolean (0/1)
            'to_waktu' => $this->faker->boolean(), // Boolean (0/1)
            'to_rute' => $this->faker->boolean(), // Boolean (0/1)
            'tr_lanjut' => Str::limit($this->faker->sentence(4), 250, ''), // Disesuaikan: max 250
            'to_lanjut' => Str::limit($this->faker->sentence(4), 250, ''), // Disesuaikan: max 250
            'tr_petugas' => $this->faker->boolean(), // Integer (0/1)
            'to_petugas' => $this->faker->boolean(), // Integer (0/1)
            'is_gofar' => $this->faker->boolean(), // Boolean (0/1)
            'is_dt' => $this->faker->boolean(), // Boolean (0/1)
            'is_onl' => $this->faker->boolean(), // Boolean (0/1)
            'is_kronis' => $this->faker->boolean(), // Boolean (0/1)
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
