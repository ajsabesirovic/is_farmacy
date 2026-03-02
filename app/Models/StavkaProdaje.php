<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StavkaProdaje extends Model
{
    use HasFactory;

    protected $table = 'stavke_prodaje';

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['prodaja_id', 'redni_broj'];

    protected $fillable = [
        'prodaja_id',
        'redni_broj',
        'lek_id',
        'kolicina',
        'cena_po_komadu',
        'popust',
    ];

    protected $casts = [
        'kolicina' => 'integer',
        'cena_po_komadu' => 'decimal:2',
        'popust' => 'decimal:2',
    ];

    public function prodaja(): BelongsTo
    {
        return $this->belongsTo(Prodaja::class, 'prodaja_id');
    }

    public function lek(): BelongsTo
    {
        return $this->belongsTo(Lek::class, 'lek_id');
    }

    public function getUkupnoAttribute(): float
    {
        return ($this->kolicina * $this->cena_po_komadu) - $this->popust;
    }

    protected function setKeysForSaveQuery($query)
    {
        $query->where('prodaja_id', $this->getAttribute('prodaja_id'))
              ->where('redni_broj', $this->getAttribute('redni_broj'));
        return $query;
    }
}
