<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lekar extends Model
{
    protected $table = 'lekari';

    protected $fillable = [
        'ime',
        'prezime',
        'broj_licence',
        'specijalnost',
    ];

    public function getFullNameAttribute(): string
    {
        return "dr {$this->ime} {$this->prezime}";
    }
}
