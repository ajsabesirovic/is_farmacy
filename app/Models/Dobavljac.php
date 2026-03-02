<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dobavljac extends Model
{
    use HasFactory;

    protected $table = 'dobavljaci';

    protected $fillable = [
        'naziv',
        'pib',
        'telefon',
        'email',
        'aktivan',
    ];

    protected $casts = [
        'aktivan' => 'boolean',
    ];

    public function lekovi(): BelongsToMany
    {
        return $this->belongsToMany(Lek::class, 'dobavljac_lek', 'dobavljac_id', 'lek_id')
                    ->withPivot('nabavna_cena');
    }

    public function narudzbenice(): HasMany
    {
        return $this->hasMany(Narudzbenica::class, 'dobavljac_id');
    }

    public function scopeAktivni($query)
    {
        return $query->where('aktivan', true);
    }
}
