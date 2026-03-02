<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrovaniKorisnik extends Model
{
    use HasFactory;

    protected $table = 'registrovani_korisnici';

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'jmbg',
    ];

    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(Korisnik::class, 'id');
    }
}
