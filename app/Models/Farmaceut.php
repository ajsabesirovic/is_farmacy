<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Farmaceut extends Model
{
    use HasFactory;

    protected $table = 'farmaceuti';

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'licenca',
    ];

    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(Korisnik::class, 'id');
    }
}
