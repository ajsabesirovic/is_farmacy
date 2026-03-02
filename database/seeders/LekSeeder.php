<?php

namespace Database\Seeders;

use App\Models\Lek;
use Illuminate\Database\Seeder;

class LekSeeder extends Seeder
{
    public function run(): void
    {
        $lekovi = [
            ['naziv' => 'Paracetamol', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL001', 'farm_oblik' => 'Tableta', 'jacina' => '500mg', 'pakovanje' => '20 tableta', 'na_recept' => false],
            ['naziv' => 'Brufen', 'proizvodjac' => 'Galenika', 'jkl_sifra' => 'JKL002', 'farm_oblik' => 'Tableta', 'jacina' => '400mg', 'pakovanje' => '30 tableta', 'na_recept' => false],
            ['naziv' => 'Aspirin', 'proizvodjac' => 'Bayer', 'jkl_sifra' => 'JKL003', 'farm_oblik' => 'Tableta', 'jacina' => '500mg', 'pakovanje' => '20 tableta', 'na_recept' => false],
            ['naziv' => 'Vitamin C', 'proizvodjac' => 'Galenika', 'jkl_sifra' => 'JKL004', 'farm_oblik' => 'Tableta', 'jacina' => '1000mg', 'pakovanje' => '20 tableta', 'na_recept' => false],
            ['naziv' => 'Vitamin D3', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL005', 'farm_oblik' => 'Kapsula', 'jacina' => '1000IU', 'pakovanje' => '60 kapsula', 'na_recept' => false],
            ['naziv' => 'Defrinol', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL006', 'farm_oblik' => 'Tableta', 'jacina' => '60mg/500mg', 'pakovanje' => '20 tableta', 'na_recept' => false],
            ['naziv' => 'Strepsils', 'proizvodjac' => 'Reckitt', 'jkl_sifra' => 'JKL007', 'farm_oblik' => 'Pastila', 'jacina' => '1.2mg', 'pakovanje' => '24 pastile', 'na_recept' => false],
            ['naziv' => 'Ranisan', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL008', 'farm_oblik' => 'Tableta', 'jacina' => '150mg', 'pakovanje' => '20 tableta', 'na_recept' => false],
            ['naziv' => 'Pantenol', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL009', 'farm_oblik' => 'Krema', 'jacina' => '5%', 'pakovanje' => '30g', 'na_recept' => false],
            ['naziv' => 'Bepanthen', 'proizvodjac' => 'Bayer', 'jkl_sifra' => 'JKL010', 'farm_oblik' => 'Mast', 'jacina' => '5%', 'pakovanje' => '30g', 'na_recept' => false],
            ['naziv' => 'Amoksicilin', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL011', 'farm_oblik' => 'Kapsula', 'jacina' => '500mg', 'pakovanje' => '16 kapsula', 'na_recept' => true],
            ['naziv' => 'Azitromicin', 'proizvodjac' => 'Galenika', 'jkl_sifra' => 'JKL012', 'farm_oblik' => 'Tableta', 'jacina' => '500mg', 'pakovanje' => '3 tablete', 'na_recept' => true],
            ['naziv' => 'Cefaleksin', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL013', 'farm_oblik' => 'Kapsula', 'jacina' => '500mg', 'pakovanje' => '16 kapsula', 'na_recept' => true],
            ['naziv' => 'Metformin', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL014', 'farm_oblik' => 'Tableta', 'jacina' => '850mg', 'pakovanje' => '30 tableta', 'na_recept' => true],
            ['naziv' => 'Enalapril', 'proizvodjac' => 'Galenika', 'jkl_sifra' => 'JKL015', 'farm_oblik' => 'Tableta', 'jacina' => '10mg', 'pakovanje' => '20 tableta', 'na_recept' => true],
            ['naziv' => 'Amlodipin', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL016', 'farm_oblik' => 'Tableta', 'jacina' => '5mg', 'pakovanje' => '30 tableta', 'na_recept' => true],
            ['naziv' => 'Atorvastatin', 'proizvodjac' => 'Galenika', 'jkl_sifra' => 'JKL017', 'farm_oblik' => 'Tableta', 'jacina' => '20mg', 'pakovanje' => '30 tableta', 'na_recept' => true],
            ['naziv' => 'Omeprazol', 'proizvodjac' => 'Hemofarm', 'jkl_sifra' => 'JKL018', 'farm_oblik' => 'Kapsula', 'jacina' => '20mg', 'pakovanje' => '14 kapsula', 'na_recept' => true],
            ['naziv' => 'Diklofenak', 'proizvodjac' => 'Galenika', 'jkl_sifra' => 'JKL019', 'farm_oblik' => 'Tableta', 'jacina' => '50mg', 'pakovanje' => '20 tableta', 'na_recept' => true],
            ['naziv' => 'Sertralin', 'proizvodjac' => 'Galenika', 'jkl_sifra' => 'JKL020', 'farm_oblik' => 'Tableta', 'jacina' => '50mg', 'pakovanje' => '28 tableta', 'na_recept' => true],
        ];

        foreach ($lekovi as $lek) {
            Lek::create($lek);
        }
    }
}
