<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenarCara extends Model
{
    use HasFactory;

    protected $table = 'bnr_cara';
    protected $primaryKey = 'id_bnr_cara';

    protected $fillable = [
        'is_oral',
        'is_iv',
        'is_im',
        'is_no_reg',
        'tanggal',
        'jam',
        'user_id',
        'keterangan',
        'no_reg',
        'no_cm',
    ];

    protected $casts = [
        'is_oral' => 'boolean',
        'is_iv' => 'boolean',
        'is_im' => 'boolean',
        'tanggal' => 'date',
    ];

    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(MasterPasien::class, 'no_cm', 'no_cm');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
