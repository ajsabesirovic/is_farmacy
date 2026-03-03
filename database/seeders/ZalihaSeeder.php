<?php

namespace Database\Seeders;

use App\Models\Zaliha;
use Illuminate\Database\Seeder;

class ZalihaSeeder extends Seeder
{
    public function run(): void
    {
        $cene = [
            1 => 250, 2 => 480, 3 => 350, 4 => 420, 5 => 580,
            6 => 620, 7 => 390, 8 => 280, 9 => 350, 10 => 450,
            11 => 850, 12 => 920, 13 => 780, 14 => 450, 15 => 380,
            16 => 520, 17 => 680, 18 => 420, 19 => 350, 20 => 890,
        ];

        for ($apotekaId = 1; $apotekaId <= 5; $apotekaId++) {
            for ($lekId = 1; $lekId <= 20; $lekId++) {
                $kolicina = rand(0, 100);

                if (($apotekaId + $lekId) % 7 == 0) {
                    $kolicina = rand(0, 5);
                }

                Zaliha::create([
                    'apoteka_id' => $apotekaId,
                    'lek_id' => $lekId,
                    'kolicina' => $kolicina,
                    'prodajna_cena' => $cene[$lekId] + rand(-50, 50),
                    'min_zaliha' => 10,
                    'datum_azuriranja' => now(),
                ]);
            }
        }
    }
}
