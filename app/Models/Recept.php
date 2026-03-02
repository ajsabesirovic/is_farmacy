<?php

namespace App\Models;

use App\Enums\PrescriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Recept extends Model
{
    use HasFactory;

    protected $table = 'recepti';

    protected $fillable = [
        'broj_recepta',
        'datum_izdavanja',
        'datum_vazenja',
        'dijagnoza_sifra',
        'status',
        'napomena',
        'ime_pacijenta',
        'jmbg_pacijenta',
        'lekar_id',
    ];

    protected $casts = [
        'datum_izdavanja' => 'date',
        'datum_vazenja' => 'date',
        'status' => PrescriptionStatus::class,
    ];

    public function lekar(): BelongsTo
    {
        return $this->belongsTo(Lekar::class, 'lekar_id');
    }

    public function lekovi(): BelongsToMany
    {
        return $this->belongsToMany(Lek::class, 'recept_lek', 'recept_id', 'lek_id')
                    ->withPivot('kolicina', 'izdata_kolicina', 'doziranje');
    }

    /**
     * Vraća preostalu količinu leka za izdavanje
     */
    public function getPreostalaKolicina(int $lekId): int
    {
        $lek = $this->lekovi()->where('lek_id', $lekId)->first();
        if (!$lek) {
            return 0;
        }
        return max(0, $lek->pivot->kolicina - $lek->pivot->izdata_kolicina);
    }

    /**
     * Proverava da li je recept potpuno realizovan (svi lekovi izdati)
     */
    public function isFullyDispensed(): bool
    {
        foreach ($this->lekovi as $lek) {
            if ($lek->pivot->izdata_kolicina < $lek->pivot->kolicina) {
                return false;
            }
        }
        return true;
    }

    /**
     * Proverava da li je recept delimično realizovan
     */
    public function isPartiallyDispensed(): bool
    {
        $hasDispensed = false;
        $hasRemaining = false;

        foreach ($this->lekovi as $lek) {
            if ($lek->pivot->izdata_kolicina > 0) {
                $hasDispensed = true;
            }
            if ($lek->pivot->izdata_kolicina < $lek->pivot->kolicina) {
                $hasRemaining = true;
            }
        }

        return $hasDispensed && $hasRemaining;
    }

    public function prodaja(): HasOne
    {
        return $this->hasOne(Prodaja::class, 'recept_id');
    }

    public function isValid(): bool
    {
        return $this->status === PrescriptionStatus::IZDAT
            && $this->datum_vazenja >= now()->toDateString();
    }

    public function isRealized(): bool
    {
        return $this->status === PrescriptionStatus::REALIZOVAN;
    }

    public function isExpired(): bool
    {
        return $this->datum_vazenja < now()->toDateString();
    }

    public function scopeNerealizovan($query)
    {
        return $query->where('status', PrescriptionStatus::IZDAT);
    }
}
