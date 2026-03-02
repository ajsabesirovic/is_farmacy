<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StavkaNarudzbenice extends Model
{
    use HasFactory;

    protected $table = 'stavke_narudzbenice';

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['narudzbenica_id', 'redni_broj'];

    protected $fillable = [
        'narudzbenica_id',
        'redni_broj',
        'lek_id',
        'kolicina',
        'cena_po_komadu',
    ];

    protected $casts = [
        'kolicina' => 'integer',
        'cena_po_komadu' => 'decimal:2',
    ];

    public function narudzbenica(): BelongsTo
    {
        return $this->belongsTo(Narudzbenica::class, 'narudzbenica_id');
    }

    public function lek(): BelongsTo
    {
        return $this->belongsTo(Lek::class, 'lek_id');
    }

    public function getUkupnoAttribute(): float
    {
        return $this->kolicina * $this->cena_po_komadu;
    }

    protected function setKeysForSaveQuery($query)
    {
        $query->where('narudzbenica_id', $this->getAttribute('narudzbenica_id'))
              ->where('redni_broj', $this->getAttribute('redni_broj'));
        return $query;
    }
}
