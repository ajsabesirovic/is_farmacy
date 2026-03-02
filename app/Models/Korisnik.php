<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Korisnik extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'korisnici';

    protected $fillable = [
        'ime',
        'prezime',
        'email',
        'lozinka_hash',
        'aktivan',
        'apoteka_id',
        'tip',
    ];

    protected $hidden = [
        'lozinka_hash',
        'remember_token',
    ];

    protected $casts = [
        'aktivan' => 'boolean',
        'tip' => UserType::class,
    ];

    public function getAuthPassword()
    {
        return $this->lozinka_hash;
    }

    public function apoteka(): BelongsTo
    {
        return $this->belongsTo(Apoteka::class, 'apoteka_id');
    }

    public function farmaceut(): HasOne
    {
        return $this->hasOne(Farmaceut::class, 'id');
    }

    public function adminApoteke(): HasOne
    {
        return $this->hasOne(AdminApoteke::class, 'id');
    }

    public function centralniAdmin(): HasOne
    {
        return $this->hasOne(CentralniAdmin::class, 'id');
    }

    public function registrovaniKorisnik(): HasOne
    {
        return $this->hasOne(RegistrovaniKorisnik::class, 'id');
    }

    public function prodaje(): HasMany
    {
        return $this->hasMany(Prodaja::class, 'korisnik_id');
    }

    public function narudzbenice(): HasMany
    {
        return $this->hasMany(Narudzbenica::class, 'korisnik_id');
    }

    public function isFarmaceut(): bool
    {
        return $this->tip === UserType::FARMACEUT;
    }

    public function isAdminApoteke(): bool
    {
        return $this->tip === UserType::ADMIN_APOTEKE;
    }

    public function isCentralniAdmin(): bool
    {
        return $this->tip === UserType::CENTRALNI_ADMIN;
    }

    public function isRegistrovaniKorisnik(): bool
    {
        return $this->tip === UserType::REGISTROVANI_KORISNIK;
    }

    public function getSubtypeData()
    {
        return match($this->tip) {
            UserType::FARMACEUT => $this->farmaceut,
            UserType::ADMIN_APOTEKE => $this->adminApoteke,
            UserType::CENTRALNI_ADMIN => $this->centralniAdmin,
            UserType::REGISTROVANI_KORISNIK => $this->registrovaniKorisnik,
            default => null,
        };
    }

    public function getPunoImeAttribute(): string
    {
        return "{$this->ime} {$this->prezime}";
    }

    public function scopeAktivni($query)
    {
        return $query->where('aktivan', true);
    }

    public function scopeTip($query, UserType $tip)
    {
        return $query->where('tip', $tip);
    }
}
