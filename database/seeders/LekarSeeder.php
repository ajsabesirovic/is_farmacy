<?php

namespace Database\Seeders;

use App\Models\Lekar;
use Illuminate\Database\Seeder;

class LekarSeeder extends Seeder
{
    public function run(): void
    {
        $lekari = [
            ['ime' => 'Marko', 'prezime' => 'Petrovic', 'broj_licence' => '12345', 'specijalnost' => 'Opsta praksa'],
            ['ime' => 'Ana', 'prezime' => 'Jovanovic', 'broj_licence' => '23456', 'specijalnost' => 'Interna medicina'],
            ['ime' => 'Nikola', 'prezime' => 'Stojanovic', 'broj_licence' => '34567', 'specijalnost' => 'Kardiologija'],
            ['ime' => 'Jelena', 'prezime' => 'Markovic', 'broj_licence' => '45678', 'specijalnost' => 'Neurologija'],
            ['ime' => 'Stefan', 'prezime' => 'Djordjevic', 'broj_licence' => '56789', 'specijalnost' => 'Dermatologija'],
        ];

        foreach ($lekari as $lekar) {
            Lekar::create($lekar);
        }
    }
}
