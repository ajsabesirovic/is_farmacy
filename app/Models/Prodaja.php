<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodaja extends Model
{
    use HasFactory;

    protected $table = 'prodaje';

    protected $fillable = [
        'datum',
        'vreme',
        'nacin_placanja',
        'apoteka_id',
        'korisnik_id',
        'recept_id',
        'ukupan_iznos',
    ];

    protected $casts = [
        'datum' => 'date',
        'nacin_placanja' => PaymentMethod::class,
        'ukupan_iznos' => 'decimal:2',
    ];

    public function apoteka(): BelongsTo
    {
        return $this->belongsTo(Apoteka::class, 'apoteka_id');
    }

    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(Korisnik::class, 'korisnik_id');
    }

    public function recept(): BelongsTo
    {
        return $this->belongsTo(Recept::class, 'recept_id');
    }

    public function stavke(): HasMany
    {
        return $this->hasMany(StavkaProdaje::class, 'prodaja_id');
    }

    public function izracunajUkupanIznos(): float
    {
        return $this->stavke->sum(function ($stavka) {
            return ($stavka->kolicina * $stavka->cena_po_komadu) - $stavka->popust;
        });
    }

    public function scopeZaApoteku($query, int $apotekaId)
    {
        return $query->where('apoteka_id', $apotekaId);
    }

    public function scopeZaPeriod($query, string $od, string $do)
    {
        return $query->whereBetween('datum', [$od, $do]);
    }
}
