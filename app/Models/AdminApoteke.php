<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminApoteke extends Model
{
    use HasFactory;

    protected $table = 'admini_apoteke';

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
    ];

    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(Korisnik::class, 'id');
    }
}
