<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zaliha extends Model
{
    use HasFactory;

    protected $table = 'zalihe';

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['apoteka_id', 'lek_id'];

    protected $fillable = [
        'apoteka_id',
        'lek_id',
        'kolicina',
        'prodajna_cena',
        'min_zaliha',
        'datum_azuriranja',
    ];

    protected $casts = [
        'kolicina' => 'integer',
        'prodajna_cena' => 'decimal:2',
        'min_zaliha' => 'integer',
        'datum_azuriranja' => 'datetime',
    ];

    public function apoteka(): BelongsTo
    {
        return $this->belongsTo(Apoteka::class, 'apoteka_id');
    }

    public function lek(): BelongsTo
    {
        return $this->belongsTo(Lek::class, 'lek_id');
    }

    public function isLowStock(): bool
    {
        return $this->kolicina <= $this->min_zaliha;
    }

    public function isOutOfStock(): bool
    {
        return $this->kolicina <= 0;
    }

    public function scopeNiskeZalihe($query)
    {
        return $query->whereRaw('kolicina <= min_zaliha');
    }

    public function scopeDostupne($query)
    {
        return $query->where('kolicina', '>', 0);
    }

    public function scopeZaApoteku($query, int $apotekaId)
    {
        return $query->where('apoteka_id', $apotekaId);
    }

    protected function setKeysForSaveQuery($query)
    {
        $query->where('apoteka_id', $this->getAttribute('apoteka_id'))
              ->where('lek_id', $this->getAttribute('lek_id'));
        return $query;
    }
}
