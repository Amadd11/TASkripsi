<?php

namespace Database\Seeders;

use App\Models\FarTransaction;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\MasterPasien;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FarTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $pasien = MasterPasien::where('no_cm', '00001234')->first();
        if (!$pasien) {
            $this->command->error('Pasien dengan no_cm 00001234 tidak ditemukan. Seeder dibatalkan.');
            return;
        }
        // Masukkan satu data transaksi farmasi contoh
        DB::table('far_transactions')->insert([
            'no_cm' => $pasien->no_cm, // Menggunakan no_cm dari pasien yang ditemukan/dibuat
            'tgl' => Carbon::now(),
            'jam' => Carbon::now()->format('H:i:s'),
            'unit' => 'Farmasi',
            'dokter' => 'dr. Budi Santoso',
            'petugas' => 'Apoteker Dina',
            'sampel' => 'Tidak Ada',
            'no_reg' => 'REG00001',
            'pengirim' => 'Poli Umum',
            'biaya' => 150000,
            'cetak' => 'Sudah',
            'lunas' => 'LUNAS',
            'tgl_lahir' => Carbon::parse($pasien->tgl_lahir), // Ambil dari data pasien
            'alamat' => $pasien->alamat, // Ambil dari data pasien
            'sex' => $pasien->sex, // Ambil dari data pasien
            'kelas' => $pasien->kelas, // Ambil dari data pasien
            'nama_pas' => $pasien->nama_pas, // Ambil dari data pasien
            'iol' => $pasien->iol, // Ambil dari data pasien
            'rujuk' => 'Tidak', // Ini bisa diisi sesuai kebutuhan
            'bl_kunj' => 'F',
            'shift' => 'P', // Pagi, Siang, Malam
            'no_psn' => 123,
            'asuransi' => 'BPJS Kesehatan',
            'biaya_pns' => 0,
            'tgl_ambil' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'jam_ambil' => Carbon::now()->addHours(1)->format('H:i:s'),
            'menit' => 60,
            'panggil' => 1,
            'racikan' => 50000,
            'bpjs' => 100000,
            'catatan' => 'Obat untuk 7 hari.',
            'loket' => 1,
            'sub_embalase' => 10000,
            'sub_er' => 0,
            'sub_racikan' => 50000,
            'sub_item_er' => 0,
            'grand_total' => 160000, // Biaya + Sub_embalase
            'bayar' => 160000,
            'emr' => 1,
            'bagian' => 0,
            'gp' => 0,
            'id_h_cp' => 0,
            'klinik_online' => 0,
            'f_terapi_plg' => 0,
            'no_tunggu' => 1,
            'vlag_saji' => 0,
            'tanggal_saji' => Carbon::now()->format('Y-m-d'),
            'jam_saji' => Carbon::now()->format('H:i:s'),
            'tr_Jelas' => 1,
            'tr_obat' => 1,
            'tr_dosis' => 1,
            'tr_rute' => 1,
            'tr_waktu' => 1,
            'tr_duplikasi' => 0,
            'tr_interaksi' => 0,
            'tr_kontradiksi' => 0,
            'to_identitas' => 1,
            'to_obat' => 1,
            'to_jumlah' => 1,
            'to_waktu' => 1,
            'to_rute' => 1,
            'tr_lanjut' => 'Tidak ada masalah.',
            'to_lanjut' => 'Verifikasi selesai.',
            'tr_petugas' => 1,
            'to_petugas' => 1,
            'is_gofar' => 0,
            'is_dt' => 0,
            'is_onl' => 0,
            'is_kronis' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        FarTransaction::factory()->count(30)->create();

        $this->command->info('Data transaksi farmasi contoh berhasil disisipkan.');
    }
}
