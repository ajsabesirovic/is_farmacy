<?php

namespace Database\Seeders;

use App\Models\DobavljacLek;
use Illuminate\Database\Seeder;

class DobavljacLekSeeder extends Seeder
{
    public function run(): void
    {
        // Hemofarm (ID 1) - lekovi 1, 5, 6, 8, 9, 11, 13, 14, 16, 18
        $hemofarmLekovi = [1, 5, 6, 8, 9, 11, 13, 14, 16, 18];
        foreach ($hemofarmLekovi as $lekId) {
            DobavljacLek::create([
                'dobavljac_id' => 1,
                'lek_id' => $lekId,
                'nabavna_cena' => rand(100, 500),
            ]);
        }

        // Galenika (ID 2) - lekovi 2, 4, 12, 15, 17, 19, 20
        $galenikaLekovi = [2, 4, 12, 15, 17, 19, 20];
        foreach ($galenikaLekovi as $lekId) {
            DobavljacLek::create([
                'dobavljac_id' => 2,
                'lek_id' => $lekId,
                'nabavna_cena' => rand(100, 500),
            ]);
        }

        // Phoenix (ID 3) - svi lekovi
        for ($lekId = 1; $lekId <= 20; $lekId++) {
            DobavljacLek::create([
                'dobavljac_id' => 3,
                'lek_id' => $lekId,
                'nabavna_cena' => rand(120, 550),
            ]);
        }

        // Pharmanova (ID 4) - lekovi bez recepta
        for ($lekId = 1; $lekId <= 10; $lekId++) {
            DobavljacLek::create([
                'dobavljac_id' => 4,
                'lek_id' => $lekId,
                'nabavna_cena' => rand(90, 400),
            ]);
        }
    }
}
