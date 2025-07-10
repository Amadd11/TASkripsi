<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterPetugas extends Model
{
    use HasFactory;

    /**
     * Primary key untuk model ini.
     */
    protected $primaryKey = 'no_urut';

    /**
     * Memberitahu Eloquent bahwa tabel ini tidak menggunakan timestamps (created_at, updated_at).
     */
    public $timestamps = false;

    /**
     * Atribut yang boleh diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'nik',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'status_menikah',
        'golongan_darah',
        'gelar_depan',
        'gelar_belakang',
        'initial',
        'nama_kalurahan',
        'nama_kecamatan',
        'nama_propinsi',
        'alamat',
        'id_telegram',
        'kode_pos',
        'telepon',
        'jabatan',
        'unit',
        'sip',
        'status_karyawan',
        'nomor_rekening',
        'nama_rekening',
        'npwp',
        'biodata_aktif',
        'paraf',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected $casts = [
        'tgl_lahir' => 'date',
        'biodata_aktif' => 'boolean',
    ];

    /**
     * Mendefinisikan relasi bahwa setiap MasterPetugas "milik" satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
