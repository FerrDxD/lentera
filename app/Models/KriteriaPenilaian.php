<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KriteriaPenilaian extends Model
{
    protected $table = 'kriteria_penilaian';

    protected $fillable = [
        'lomba_id',
        'nama_kriteria',
        'bobot',
        'skala_max',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'float',
            'skala_max' => 'integer',
        ];
    }

    public function lomba(): BelongsTo
    {
        return $this->belongsTo(Lomba::class, 'lomba_id');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'kriteria_id');
    }
}
