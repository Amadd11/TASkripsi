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
        'is_intratekal',
        'is_subkutan',
        'is_sublingual',
        'is_rektal',
        'is_vaginal',
        'is_okular',
        'is_otik',
        'is_nasal',
        'is_nebulisasi',
        'is_topikal',
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
        'is_intratekal' => 'boolean',
        'is_subkutan' => 'boolean',
        'is_sublingual' => 'boolean',
        'is_rektal' => 'boolean',
        'is_vaginal' => 'boolean',
        'is_okular' => 'boolean',
        'is_otik' => 'boolean',
        'is_nasal' => 'boolean',
        'is_nebulisasi' => 'boolean',
        'is_topikal' => 'boolean',
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
