<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'far_transactions'; // Nama tabel di database

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'no_trn'; // Kolom primary key

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
    public $timestamps = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_cm',
        'tgl',
        'jam',
        'unit',
        'dokter',
        'petugas',
        'sampel',
        'no_reg',
        'pengirim',
        'biaya',
        'cetak',
        'lunas',
        'tgl_lahir',
        'alamat',
        'sex',
        'kelas',
        'nama_pas',
        'iol',
        'rujuk',
        'bl_kunj',
        'shift',
        'no_psn',
        'asuransi',
        'biaya_pns',
        'tgl_ambil',
        'jam_ambil',
        'menit',
        'panggil',
        'racikan',
        'bpjs',
        'catatan',
        'loket',
        'sub_embalase',
        'sub_er',
        'sub_racikan',
        'sub_item_er',
        'grand_total',
        'bayar',
        'emr',
        'bagian',
        'gp',
        'id_h_cp',
        'klinik_online',
        'f_terapi_plg',
        'no_tunggu',
        'vlag_saji',
        'tanggal_saji',
        'jam_saji',
        'tr_Jelas',
        'tr_obat',
        'tr_dosis',
        'tr_rute',
        'tr_waktu',
        'tr_duplikasi',
        'tr_interaksi',
        'tr_kontradiksi',
        'to_identitas',
        'to_obat',
        'to_jumlah',
        'to_waktu',
        'to_rute',
        'tr_lanjut',
        'to_lanjut',
        'tr_petugas',
        'to_petugas',
        'is_gofar',
        'is_dt',
        'is_onl',
        'is_kronis',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl' => 'datetime',
        'jam' => 'datetime:H:i:s', // Cast as time, format example
        'tgl_lahir' => 'datetime',
        'vlag_saji' => 'boolean',
        'tanggal_saji' => 'date',
        'tr_Jelas' => 'boolean',
        'tr_obat' => 'boolean',
        'tr_dosis' => 'boolean',
        'tr_rute' => 'boolean',
        'tr_waktu' => 'boolean',
        'tr_duplikasi' => 'boolean',
        'tr_interaksi' => 'boolean',
        'tr_kontradiksi' => 'boolean',
        'to_identitas' => 'boolean',
        'to_obat' => 'boolean',
        'to_jumlah' => 'boolean',
        'to_waktu' => 'boolean',
        'to_rute' => 'boolean',
        'is_gofar' => 'boolean',
        'is_dt' => 'boolean',
        'is_onl' => 'boolean',
        'is_kronis' => 'boolean',
    ];

    /**
     * Get the master pasien that owns the transaction.
     */
    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }
}
