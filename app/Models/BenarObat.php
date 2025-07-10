<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarObat extends Model
{
    //
    use HasFactory;


    protected $table = 'bnr_obat'; // Menentukan nama tabel secara eksplisit

    protected $primaryKey = 'id_bnr_obat'; // Menentukan primary key secara eksplisit

    protected $fillable = [
        'is_nama_obat',
        'is_label',
        'is_no_reg',
        'tanggal',
        'jam',
        'user_id',
        'keterangan',
        'no_reg', // Foreign key
        'no_cm',  // Foreign key
        'is_resep',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_nama_obat' => 'boolean',
        'is_label' => 'boolean',
        'tanggal' => 'date', // Mengubah ke tipe date
        'is_resep' => 'boolean',
    ];

    // --- Relasi ---

    /**
     * Get the far transaction that owns the bnr obat.
     */
    public function farTransaction(): BelongsTo
    {
        return $this->belongsTo(FarTransaction::class, 'no_reg', 'no_reg');
    }

    /**
     * Get the master pasien that owns the bnr obat.
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
