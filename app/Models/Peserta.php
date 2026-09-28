<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peserta extends Model
{
    protected $table = 'peserta';

    protected $fillable = [
        'lomba_id',
        'nomor_urut',
        'nama',
        'asal_kelas',
    ];

    public function lomba(): BelongsTo
    {
        return $this->belongsTo(Lomba::class, 'lomba_id');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'peserta_id');
    }
}
