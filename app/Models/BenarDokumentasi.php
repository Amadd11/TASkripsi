<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarDokumentasi extends Model
{
    //
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bnr_dokumentasi'; // Menentukan nama tabel secara eksplisit

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_bnr_dokumentasi'; // Menentukan primary key secara eksplisit

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_pasien',
        'is_dosis',
        'is_no_reg',
        'tanggal',
        'jam',
        'id_petugas',
        'keterangan',
        'no_reg', // Foreign key
        'no_cm',  // Foreign key
        'is_obat',
        'is_waktu',
        'is_rute',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_pasien' => 'boolean',
        'is_dosis' => 'boolean',
        'tanggal' => 'date', // Mengubah ke tipe date
        'is_obat' => 'boolean',
        'is_waktu' => 'boolean',
        'is_rute' => 'boolean',
    ];

    // --- Relasi ---

    /**
     * Get the far transaction that owns the bnr dokumentasi.
     */
    public function farTransaction(): BelongsTo
    {
        return $this->belongsTo(FarTransaction::class, 'no_reg', 'no_reg');
    }

    /**
     * Get the master pasien that owns the bnr dokumentasi.
     */
    public function masterPasien(): BelongsTo
    {
        // Alternatif: Anda bisa menggunakan nama relasi 'pasien' seperti di model BenarCara
        // return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }
}
