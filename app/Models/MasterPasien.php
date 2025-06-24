<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPasien extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_pasien'; // Nama tabel di database

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'nourut'; // Kolom primary key

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

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
        'iol',
        'tgl_kunj',
        'no_cm',
        'nama_pas',
        'tgl_lahir',
        'alamat',
        'kec',
        'nama_ortu',
        'pek_pasien',
        'pek_ortu',
        'alm_ortu',
        'sex',
        'kelas',
        'no_reg',
        'unit',
        'pend',
        'agama',
        'jam',
        'diagnosa',
        'bl',
        'identitas',
        'pengirim',
        'gol',
        'gawat',
        'al_kir',
        'nama_peng',
        'pin',
        'tgl_kunj1',
        'tgl_kunj2',
        'desa',
        'jam1',
        'jam2',
        'kls1',
        'kls2',
        'kkk',
        'kdu',
        'status',
        'hub',
        'kab',
        'telp',
        'asuransi',
        'aktif',
        'no_px',
        'petugas_tpp',
        'dokter',
        'perawat',
        'tgl_pl',
        'jam_pl',
        'bbbb',
        'kd_dx',
        'waktu',
        'telp_ortu',
        'kunjungan',
        'cara_masuk',
        'polisi',
        'ruang',
        'tgl_inap',
        'jam_inap',
        'status_kary',
        'gzresp',
        'cek',
        'id_menikah',
        'id_propinsi',
        'no_kpsta',
        'asal_daerah',
        'id_retensi',
        'id_alergi',
        'fis_asuhan',
        'catatan_bpjs',
        'cek_kpsta',
        'cek_ktp',
        'cek_kk',
        'bank_v_lab',
        'bank_v_far',
        'bank_v_rad',
        'bank_v_gz',
        'bank_v_fis',
        'nik',
        'flag_mcu',
        'flag_penyakit',
        'flag_pasien',
        'flag_status',
        'flag_prolanis',
        'paraf',
        'id_prop_domisili',
        'id_kab_domisili',
        'id_kec_domisili',
        'id_desa_domisili',
        'alamat_domisili',
        'lokasi_domisili',
        'ihs',
        'reg_sitb',
        'no_sep',
        'ihs_labmu',
        'is_hiv',
        'is_hbs',
        'is_tbc',
        'is_pulang',
        'is_titip',
        'is_farmasi',
        'is_radiologi',
        'is_laboratorium',
        'tempat',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_kunj' => 'datetime',
        'tgl_lahir' => 'datetime',
        'jam' => 'datetime:H:i:s', // Cast as time, format example
        'tgl_kunj1' => 'datetime',
        'tgl_kunj2' => 'datetime',
        'jam1' => 'datetime:H:i:s', // Cast as time, format example
        'jam2' => 'datetime:H:i:s', // Cast as time, format example
        'tgl_pl' => 'datetime',
        'jam_pl' => 'datetime:H:i:s', // Cast as time, format example
        'tgl_inap' => 'datetime',
        'cek_kpsta' => 'boolean',
        'cek_ktp' => 'boolean',
        'cek_kk' => 'boolean',
        'bank_v_lab' => 'boolean',
        'bank_v_far' => 'boolean',
        'bank_v_rad' => 'boolean',
        'bank_v_gz' => 'boolean',
        'bank_v_fis' => 'boolean',
        'flag_mcu' => 'boolean',
        'flag_penyakit' => 'boolean',
        'flag_pasien' => 'boolean',
        'flag_prolanis' => 'boolean',
        'is_hiv' => 'boolean',
        'is_hbs' => 'boolean',
        'is_tbc' => 'boolean',
        'is_pulang' => 'boolean',
        'is_titip' => 'boolean',
        'is_farmasi' => 'boolean',
        'is_radiologi' => 'boolean',
        'is_laboratorium' => 'boolean',
    ];

    /**
     * Get the transactions for the patient.
     */
    public function farTransactions(): HasMany
    {
        return $this->hasMany(FarTransaction::class, 'no_cm', 'no_cm');
    }
}
