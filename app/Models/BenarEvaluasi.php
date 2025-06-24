<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarEvaluasi extends Model
{
    //
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bnr_evaluasi'; // Menentukan nama tabel secara eksplisit

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_bnr_evaluasi'; // Menentukan primary key secara eksplisit

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_efek_samping',
        'is_alergi',
        'is_no_reg',
        'tanggal',
        'jam',
        'id_petugas',
        'keterangan',
        'no_reg', // Foreign key
        'no_cm',  // Foreign key
        'is_efek_terapi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_efek_samping' => 'boolean',
        'is_alergi' => 'boolean',
        'tanggal' => 'date', // Mengubah ke tipe date
        'is_efek_terapi' => 'boolean',
    ];

    // --- Relasi ---

    /**
     * Get the far transaction that owns the bnr evaluasi.
     */
    public function farTransaction(): BelongsTo
    {
        return $this->belongsTo(FarTransaction::class, 'no_reg', 'no_reg');
    }

    /**
     * Get the master pasien that owns the bnr evaluasi.
     */
    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }
}
