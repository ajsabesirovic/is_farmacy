<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lek extends Model
{
    use HasFactory;

    protected $table = 'lekovi';

    protected $fillable = [
        'naziv',
        'proizvodjac',
        'jkl_sifra',
        'farm_oblik',
        'jacina',
        'pakovanje',
        'na_recept',
    ];

    protected $casts = [
        'na_recept' => 'boolean',
    ];

    public function zalihe(): HasMany
    {
        return $this->hasMany(Zaliha::class, 'lek_id');
    }

    public function apoteke(): BelongsToMany
    {
        return $this->belongsToMany(Apoteka::class, 'zalihe', 'lek_id', 'apoteka_id')
                    ->withPivot('kolicina', 'prodajna_cena', 'min_zaliha');
    }

    public function dobavljaci(): BelongsToMany
    {
        return $this->belongsToMany(Dobavljac::class, 'dobavljac_lek', 'lek_id', 'dobavljac_id')
                    ->withPivot('nabavna_cena');
    }

    public function recepti(): BelongsToMany
    {
        return $this->belongsToMany(Recept::class, 'recept_lek', 'lek_id', 'recept_id')
                    ->withPivot('kolicina', 'doziranje');
    }

    public function stavkeProdaje(): HasMany
    {
        return $this->hasMany(StavkaProdaje::class, 'lek_id');
    }

    public function stavkeNarudzbenice(): HasMany
    {
        return $this->hasMany(StavkaNarudzbenice::class, 'lek_id');
    }

    public function scopeNaRecept($query)
    {
        return $query->where('na_recept', true);
    }

    public function scopeBezRecepta($query)
    {
        return $query->where('na_recept', false);
    }

    public function getDostupnostAttribute()
    {
        return $this->zalihe()
            ->where('kolicina', '>', 0)
            ->with('apoteka')
            ->get();
    }
}
