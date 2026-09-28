<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lomba extends Model
{
    protected $table = 'lomba';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'status',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'locked_at' => 'datetime',
        ];
    }

    public static function booted(): void
    {
        static::creating(function ($lomba) {
            if (empty($lomba->slug)) {
                $lomba->slug = Str::slug($lomba->nama);
            }
        });
    }

    public function kriteria(): HasMany
    {
        return $this->hasMany(KriteriaPenilaian::class, 'lomba_id');
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class, 'lomba_id')->orderBy('nomor_urut');
    }

    public function juri(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'juri_lomba', 'lomba_id', 'user_id');
    }

    public function juriLomba(): HasMany
    {
        return $this->hasMany(JuriLomba::class, 'lomba_id');
    }

    public function isLocked(): bool
    {
        return $this->status === 'dikunci' || $this->locked_at !== null;
    }
}
