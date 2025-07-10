<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarPengkajian extends Model
{
    //
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bnr_pengkajian'; // Menentukan nama tabel secara eksplisit

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_bnr_pengkajian'; // Menentukan primary key secara eksplisit

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_suhu',
        'is_tensi',
        'is_no_reg',
        'tanggal',
        'jam',
        'user_id',
        'keterangan',
        'no_reg', // Foreign key
        'no_cm',  // Foreign key
    ];

    protected $casts = [
        'is_suhu' => 'boolean',
        'is_tensi' => 'boolean',
        'tanggal' => 'date', // Mengubah ke tipe date
    ];

    // --- Relasi ---

    /**
     * Get the far transaction that owns the bnr pengkajian.
     */
    public function farTransaction(): BelongsTo
    {
        return $this->belongsTo(FarTransaction::class, 'no_reg', 'no_reg');
    }

    /**
     * Get the master pasien that owns the bnr pengkajian.
     */
    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
