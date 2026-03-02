<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Apoteka extends Model
{
    use HasFactory;

    protected $table = 'apoteke';

    protected $fillable = [
        'naziv',
        'adresa',
        'grad',
        'telefon',
        'email',
        'aktivna',
    ];

    protected $casts = [
        'aktivna' => 'boolean',
    ];

    public function korisnici(): HasMany
    {
        return $this->hasMany(Korisnik::class, 'apoteka_id');
    }

    public function zalihe(): HasMany
    {
        return $this->hasMany(Zaliha::class, 'apoteka_id');
    }

    public function lekovi(): BelongsToMany
    {
        return $this->belongsToMany(Lek::class, 'zalihe', 'apoteka_id', 'lek_id')
                    ->withPivot('kolicina', 'prodajna_cena', 'min_zaliha');
    }

    public function prodaje(): HasMany
    {
        return $this->hasMany(Prodaja::class, 'apoteka_id');
    }

    public function narudzbenice(): HasMany
    {
        return $this->hasMany(Narudzbenica::class, 'apoteka_id');
    }

    public function scopeAktivne($query)
    {
        return $query->where('aktivna', true);
    }
}
