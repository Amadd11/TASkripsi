<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarDosis extends Model
{
    //
    use HasFactory;

    protected $table = 'bnr_dosis'; // Menentukan nama tabel secara eksplisit

    protected $primaryKey = 'id_bnr_dosis'; // Menentukan primary key secara eksplisit

    protected $fillable = [
        'is_jumlah',
        'is_potensi',
        'is_no_reg',
        'tanggal',
        'jam',
        'user_id',
        'keterangan',
        'no_reg',
        'no_cm',
    ];


    protected $casts = [
        'is_jumlah' => 'boolean',
        'is_potensi' => 'boolean',
        'tanggal' => 'date', // Mengubah ke tipe date
    ];


    /**
     * Get the master pasien that owns the bnr dosis.
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
