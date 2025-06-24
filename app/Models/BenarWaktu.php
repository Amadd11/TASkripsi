<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarWaktu extends Model
{
    //
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bnr_waktu'; // Explicitly setting the table name

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_bnr_waktu'; // Explicitly setting the primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_pagi',
        'is_siang',
        'is_no_reg',
        'tanggal',
        'jam',
        'id_petugas',
        'keterangan',
        'no_reg', // Foreign key
        'no_cm',  // Foreign key
        'is_sore',
        'is_malam',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_pagi' => 'boolean',
        'is_siang' => 'boolean',
        'tanggal' => 'date', // Casting to date type
        'is_sore' => 'boolean',
        'is_malam' => 'boolean',
    ];

    // --- Relationships ---

    /**
     * Get the far transaction that owns the bnr waktu.
     */
    public function farTransaction(): BelongsTo
    {
        return $this->belongsTo(FarTransaction::class, 'no_reg', 'no_reg');
    }

    /**
     * Get the master pasien that owns the bnr waktu.
     */
    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }
}
