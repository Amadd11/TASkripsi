<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    use HasFactory;

    protected $fillable = [
        // Data Transaksi
        'no_cm',
        'no_reg',
        'tanggal',
        'jam',
        'user_id',
        'status', // 'PRA' atau 'SELESAI'

        // Pra Tindakan
        'pasien_is_nama',
        'pasien_is_tgl_lahir',
        'pasien_keterangan',
        'obat_is_nama_obat',
        'obat_is_label',
        'obat_is_resep',
        'obat_keterangan',
        'dosis_is_jumlah',
        'dosis_is_potensi',
        'dosis_keterangan',
        'cara_is_oral',
        'cara_is_iv',
        'cara_is_im',
        'cara_keterangan',
        'waktu_is_pagi',
        'waktu_is_siang',
        'waktu_is_sore',
        'waktu_is_malam',
        'waktu_keterangan',
        'pengkajian_is_suhu',
        'pengkajian_is_tensi',
        'pengkajian_keterangan',
        'hak_is_ic',
        'hak_keterangan',

        // Pasca Tindakan
        'dok_is_pasien',
        'dok_is_dosis',
        'dok_is_obat',
        'dok_is_waktu',
        'dok_is_rute',
        'dok_keterangan',
        'evaluasi_is_efek_samping',
        'evaluasi_is_alergi',
        'evaluasi_is_efek_terapi',
        'evaluasi_keterangan',
        'reaksi_obat_is_efek_samping',
        'reaksi_obat_is_alergi',
        'reaksi_obat_is_efek_terapi',
        'reaksi_obat_keterangan',
        'reaksi_makanan_is_efek_makanan',
        'reaksi_makanan_keterangan',
        'pendidikan_is_edukasi',
        'pendidikan_keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pasien_is_nama' => 'boolean',
        // ... (Tambahkan casts untuk semua boolean fields)
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function pasien()
    {
        return $this->belongsTo(\App\Models\MasterPasien::class, 'no_cm', 'no_cm');
    }
}
