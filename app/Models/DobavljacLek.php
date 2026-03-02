<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DobavljacLek extends Model
{
    use HasFactory;

    protected $table = 'dobavljac_lek';

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['dobavljac_id', 'lek_id'];

    protected $fillable = [
        'dobavljac_id',
        'lek_id',
        'nabavna_cena',
    ];

    protected $casts = [
        'nabavna_cena' => 'decimal:2',
    ];

    public function dobavljac(): BelongsTo
    {
        return $this->belongsTo(Dobavljac::class, 'dobavljac_id');
    }

    public function lek(): BelongsTo
    {
        return $this->belongsTo(Lek::class, 'lek_id');
    }

    protected function setKeysForSaveQuery($query)
    {
        $query->where('dobavljac_id', $this->getAttribute('dobavljac_id'))
              ->where('lek_id', $this->getAttribute('lek_id'));
        return $query;
    }
}
