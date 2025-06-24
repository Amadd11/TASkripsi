<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\MasterPasien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterPasien>
 */
class MasterPasienFactory extends Factory
{
    protected $model = MasterPasien::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['L', 'P']);
        $isReturningPatient = $this->faker->boolean(70); // 70% chance of being a returning patient

        return [
            'iol' => $this->faker->randomElement(['I', 'O']),
            'tgl_kunj' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'no_cm' => $this->faker->unique()->numerify('########'),
            'nama_pas' => Str::limit($this->faker->name($gender == 'L' ? 'male' : 'female'), 40, ''), // '' untuk menghilangkan ellipsis
            'tgl_lahir' => $this->faker->dateTimeBetween('-60 years', '-10 years'),
            'alamat' => Str::limit($this->faker->address(), 200,),
            'kec' => Str::limit($this->faker->citySuffix(), 10, ''),
            'nama_ortu' => Str::limit($this->faker->name(), 30, ''),
            'pek_pasien' => Str::limit($this->faker->jobTitle(), 30, ''), // Disesuaikan
            'pek_ortu' => Str::limit($this->faker->jobTitle(), 30, ''), // Disesuaikan
            'alm_ortu' => Str::limit($this->faker->address(), 200, ''),
            'sex' => $gender,
            'kelas' => $this->faker->randomElement(['I', 'II', 'III', 'VIP']), // Max length 3, schema is 5
            'no_reg' => $this->faker->unique()->numerify('REG#####'), // Disesuaikan menjadi 8 karakter
            'unit' => Str::limit($this->faker->randomElement(['Poli Umum', 'UGD', 'Rawat Inap', 'Poli Gigi', 'Poli Anak']), 20, ''), // Disesuaikan menjadi 20
            'pend' => Str::limit($this->faker->randomElement(['SD', 'SMP', 'SMA', 'D1', 'D3', 'S1', 'S2', 'S3']), 20, ''),
            'agama' => Str::limit($this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']), 10, ''),
            'jam' => $this->faker->time('H:i:s'),
            'diagnosa' => Str::limit($this->faker->sentence(3), 50, ''),
            'bl' => $this->faker->randomLetter(),
            'identitas' => Str::limit($this->faker->randomElement(['KTP', 'SIM', 'Paspor', 'Kartu Pelajar']), 30, ''), // Disesuaikan menjadi 30
            'pengirim' => Str::limit($this->faker->company(), 40, ''),
            'gol' => $this->faker->randomElement(['A', 'B', 'AB', 'O']), // Max length 2
            'gawat' => $this->faker->boolean() ? 'Ya' : 'Tidak', // Max length 5, schema is 10
            'al_kir' => Str::limit($this->faker->randomElement(['RSUD', 'Puskesmas', 'Klinik Swasta', 'Dokter Praktik']), 45, ''), // Disesuaikan menjadi 45
            'nama_peng' => Str::limit($this->faker->name(), 45, ''), // Disesuaikan menjadi 45
            'pin' => $this->faker->randomElement(['Y', 'N']),
            'tgl_kunj1' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'tgl_kunj2' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'desa' => Str::limit($this->faker->city(), 15, ''),
            'jam1' => $this->faker->time('H:i:s'),
            'jam2' => $this->faker->time('H:i:s'),
            'kls1' => $this->faker->randomLetter(),
            'kls2' => $this->faker->randomLetter(),
            'kkk' => $this->faker->numerify('##'),
            'kdu' => $this->faker->numerify('##'),
            'status' => Str::limit($this->faker->randomElement(['Menikah', 'Belum Menikah', 'Cerai Hidup', 'Cerai Mati']), 30, ''),
            'hub' => Str::limit($this->faker->randomElement(['Keluarga Inti', 'Keluarga Lain', 'Teman', 'Sendiri']), 15, ''),
            'kab' => Str::limit($this->faker->city(), 10, ''),
            'telp' => Str::limit($this->faker->phoneNumber(), 20, ''),
            'asuransi' => Str::limit($this->faker->randomElement(['BPJS Kesehatan', 'Asuransi Swasta A', 'Asuransi Swasta B', 'Umum', 'Perusahaan']), 40, ''),
            'aktif' => Str::limit($this->faker->boolean() ? 'AKTIF' : 'NONAKTIF', 6, ''),
            'no_px' => $isReturningPatient ? $this->faker->numberBetween(1, 20) : 1,
            'petugas_tpp' => Str::limit($this->faker->name(), 40, ''), // Disesuaikan menjadi 40
            'dokter' => Str::limit($this->faker->name('male'), 40, ''), // Disesuaikan menjadi 40
            'perawat' => Str::limit($this->faker->name('female'), 40, ''), // Disesuaikan menjadi 40
            'tgl_pl' => $this->faker->dateTimeBetween('now', '+1 month'),
            'jam_pl' => $this->faker->time('H:i:s'),
            'bbbb' => Str::limit($this->faker->word(), 10, ''),
            'kd_dx' => Str::limit($this->faker->bothify('?##.???'), 6, ''), // Disesuaikan: Tambahkan Str::limit untuk memastikan 6 karakter
            'waktu' => Str::limit($this->faker->randomElement(['Pagi', 'Siang', 'Sore', 'Malam']), 6, ''), // Disesuaikan menjadi 6
            'telp_ortu' => Str::limit($this->faker->phoneNumber(), 20, ''),
            'kunjungan' => $this->faker->numberBetween(1, 15),
            'cara_masuk' => Str::limit($this->faker->randomElement(['Jalan Kaki', 'Ambulans', 'Rujukan Dokter', 'Rujukan Puskesmas']), 20, ''),
            'polisi' => Str::limit($this->faker->boolean() ? 'Ya' : 'Tidak', 5, ''),
            'ruang' => $this->faker->randomNumber(3, true),
            'tgl_inap' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'jam_inap' => $this->faker->time('H:i:s'), // Hasilnya HH:MM:SS (8 karakter), sesuai char(8)
            'status_kary' => Str::limit($this->faker->randomElement(['PNS', 'Swasta', 'Wiraswasta', 'Pelajar', 'Mahasiswa', 'Tidak Bekerja']), 30, ''),
            'gzresp' => 'T',
            'cek' => $this->faker->boolean(),
            'id_menikah' => $this->faker->numberBetween(1, 4),
            'id_propinsi' => Str::limit($this->faker->stateAbbr(), 2, ''),
            'no_kpsta' => Str::limit($this->faker->bothify('KPSTA#####??'), 30, ''),
            'asal_daerah' => Str::limit($this->faker->city(), 40, ''),
            'id_retensi' => $this->faker->randomNumber(1, false),
            'id_alergi' => $this->faker->boolean(),
            'fis_asuhan' => $this->faker->boolean(),
            'catatan_bpjs' => Str::limit($this->faker->paragraph(2), 200, ''),
            'cek_kpsta' => $this->faker->boolean(),
            'cek_ktp' => $this->faker->boolean(),
            'cek_kk' => $this->faker->boolean(),
            'bank_v_lab' => $this->faker->boolean(),
            'bank_v_far' => $this->faker->boolean(),
            'bank_v_rad' => $this->faker->boolean(),
            'bank_v_gz' => $this->faker->boolean(),
            'bank_v_fis' => $this->faker->boolean(),
            'nik' => $this->faker->unique()->numerify('################'), // Menghasilkan 16 digit, schema 20 char, aman
            'flag_mcu' => $this->faker->boolean(),
            'flag_penyakit' => $this->faker->boolean(),
            'flag_pasien' => $this->faker->boolean(),
            'flag_status' => $this->faker->boolean(), // Integer, boolean (0/1) compatible
            'flag_prolanis' => $this->faker->boolean(),
            'paraf' => Str::limit($this->faker->sentence(5), 200, ''), // text type, tapi limit untuk konsistensi/display
            'id_prop_domisili' => Str::limit($this->faker->stateAbbr(), 10, ''),
            'id_kab_domisili' => Str::limit($this->faker->citySuffix(), 10, ''),
            'id_kec_domisili' => Str::limit($this->faker->streetName(), 10, ''),
            'id_desa_domisili' => Str::limit($this->faker->streetName(), 10, ''),
            'alamat_domisili' => Str::limit($this->faker->address(), 200, ''),
            'lokasi_domisili' => Str::limit($this->faker->secondaryAddress(), 250, ''),
            'ihs' => $this->faker->uuid(), // UUID (36 chars), schema 100 char, aman
            'reg_sitb' => Str::limit($this->faker->bothify('REG-SITB-######'), 100, ''),
            'no_sep' => $this->faker->unique()->numerify('SEP-#############'), // Panjang 16, schema 20 char, aman
            'ihs_labmu' => $this->faker->uuid(), // UUID (36 chars), schema 50 char, aman
            'is_hiv' => $this->faker->boolean(10),
            'is_hbs' => $this->faker->boolean(10),
            'is_tbc' => $this->faker->boolean(10),
            'is_pulang' => $this->faker->boolean(),
            'is_titip' => $this->faker->boolean(),
            'is_farmasi' => $this->faker->boolean(),
            'is_radiologi' => $this->faker->boolean(),
            'is_laboratorium' => $this->faker->boolean(),
            'tempat' => Str::limit($this->faker->word(), 50, ''),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
