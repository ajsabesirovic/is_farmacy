<?php

namespace Database\Seeders;

use App\Models\Apoteka;
use Illuminate\Database\Seeder;

class ApotekaSeeder extends Seeder
{
    public function run(): void
    {
        $apoteke = [
            [
                'naziv' => 'Apoteka Centar',
                'adresa' => 'Knez Mihailova 15',
                'grad' => 'Beograd',
                'telefon' => '011/123-4567',
                'email' => 'centar@apoteke.rs',
                'aktivna' => true,
            ],
            [
                'naziv' => 'Apoteka Novi Beograd',
                'adresa' => 'Bulevar Mihajla Pupina 10',
                'grad' => 'Beograd',
                'telefon' => '011/234-5678',
                'email' => 'novibeograd@apoteke.rs',
                'aktivna' => true,
            ],
            [
                'naziv' => 'Apoteka Novi Sad',
                'adresa' => 'Bulevar Oslobodjenja 55',
                'grad' => 'Novi Sad',
                'telefon' => '021/456-7890',
                'email' => 'novisad@apoteke.rs',
                'aktivna' => true,
            ],
            [
                'naziv' => 'Apoteka Nis',
                'adresa' => 'Obrenoviceva 12',
                'grad' => 'Nis',
                'telefon' => '018/567-8901',
                'email' => 'nis@apoteke.rs',
                'aktivna' => true,
            ],
            [
                'naziv' => 'Apoteka Kragujevac',
                'adresa' => 'Kralja Petra I 25',
                'grad' => 'Kragujevac',
                'telefon' => '034/678-9012',
                'email' => 'kragujevac@apoteke.rs',
                'aktivna' => true,
            ],
        ];

        foreach ($apoteke as $apoteka) {
            Apoteka::create($apoteka);
        }
    }
}
