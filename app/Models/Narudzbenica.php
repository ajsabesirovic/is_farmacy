<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Narudzbenica extends Model
{
    use HasFactory;

    protected $table = 'narudzbenice';

    protected $fillable = [
        'broj_narudzbenice',
        'datum_kreiranja',
        'datum_isporuke',
        'status',
        'napomena',
        'apoteka_id',
        'dobavljac_id',
        'korisnik_id',
        'ukupna_vrednost',
    ];

    protected $casts = [
        'datum_kreiranja' => 'datetime',
        'datum_isporuke' => 'date',
        'status' => OrderStatus::class,
        'ukupna_vrednost' => 'decimal:2',
    ];

    public function apoteka(): BelongsTo
    {
        return $this->belongsTo(Apoteka::class, 'apoteka_id');
    }

    public function dobavljac(): BelongsTo
    {
        return $this->belongsTo(Dobavljac::class, 'dobavljac_id');
    }

    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(Korisnik::class, 'korisnik_id');
    }

    public function stavke(): HasMany
    {
        return $this->hasMany(StavkaNarudzbenice::class, 'narudzbenica_id');
    }

    public function izracunajUkupnuVrednost(): float
    {
        return $this->stavke->sum(function ($stavka) {
            return $stavka->kolicina * $stavka->cena_po_komadu;
        });
    }

    public function scopeZaApoteku($query, int $apotekaId)
    {
        return $query->where('apoteka_id', $apotekaId);
    }

    public function scopeStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }
}
