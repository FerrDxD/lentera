<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JuriLomba extends Model
{
    protected $table = 'juri_lomba';

    protected $fillable = [
        'user_id',
        'lomba_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lomba(): BelongsTo
    {
        return $this->belongsTo(Lomba::class, 'lomba_id');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'juri_lomba_id');
    }
}
