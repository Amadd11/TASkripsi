<?php

namespace Database\Seeders;

use App\Models\BenarCara;
use App\Models\BenarDokumentasi;
use App\Models\BenarDosis;
use App\Models\BenarEvaluasi;
use App\Models\BenarHakClient;
use App\Models\BenarObat;
use App\Models\BenarPasien;
use App\Models\BenarPendidikan;
use App\Models\BenarPengkajian;
use App\Models\BenarReaksiMakanan;
use App\Models\BenarReaksiObat;
use App\Models\BenarWaktu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Prinsip12BenarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BenarCara::factory()->count(50)->create();
        BenarReaksiObat::factory()->count(50)->create();
        BenarDosis::factory()->count(50)->create();
        BenarDokumentasi::factory()->count(50)->create();
        BenarEvaluasi::factory()->count(50)->create();
        BenarPendidikan::factory()->count(50)->create();
        BenarPengkajian::factory()->count(50)->create();
        BenarObat::factory()->count(50)->create();
        BenarPasien::factory()->count(50)->create();
        BenarReaksiMakanan::factory()->count(50)->create();
        BenarWaktu::factory()->count(50)->create();
        BenarHakClient::factory()->count(50)->create();
    }
}
