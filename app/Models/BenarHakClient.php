<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarHakClient extends Model
{
    //
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bnr_hak_client'; // Menentukan nama tabel secara eksplisit

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_bnr_hak_client'; // Menentukan primary key secara eksplisit

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; // Mengaktifkan timestamps (created_at, updated_at)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_ic',
        'is_no_reg',
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
        'is_ic' => 'boolean',
        'tanggal' => 'date', // Mengubah ke tipe date
    ];

    // --- Relasi ---

    /**
     * Get the far transaction that owns the bnr hak client.
     */
    public function farTransaction(): BelongsTo
    {
        return $this->belongsTo(FarTransaction::class, 'no_reg', 'no_reg');
    }

    /**
     * Get the master pasien that owns the bnr hak client.
     */
    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }
}
