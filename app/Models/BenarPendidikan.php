<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarPendidikan extends Model
{
    //
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bnr_pendidikan'; // Menentukan nama tabel secara eksplisit

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_bnr_pendidikan'; // Menentukan primary key secara eksplisit

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_edukasi',
        'tanggal',
        'jam',
        'id_petugas',
        'keterangan',
        'no_reg', // Foreign key
        'no_cm',  // Foreign key
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_edukasi' => 'boolean',
        'tanggal' => 'date', // Mengubah ke tipe date
    ];

    // --- Relasi ---

    /**
     * Get the far transaction that owns the bnr pendidikan.
     */
    public function farTransaction(): BelongsTo
    {
        return $this->belongsTo(FarTransaction::class, 'no_reg', 'no_reg');
    }

    /**
     * Get the master pasien that owns the bnr pendidikan.
     */
    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }
}
