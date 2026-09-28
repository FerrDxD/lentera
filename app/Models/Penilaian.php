<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'juri_lomba_id',
        'peserta_id',
        'kriteria_id',
        'skor',
    ];

    protected function casts(): array
    {
        return [
            'skor' => 'float',
        ];
    }

    public function juriLomba(): BelongsTo
    {
        return $this->belongsTo(JuriLomba::class, 'juri_lomba_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(KriteriaPenilaian::class, 'kriteria_id');
    }
}
