<?php

namespace Database\Seeders;

use App\Models\Dobavljac;
use Illuminate\Database\Seeder;

class DobavljacSeeder extends Seeder
{
    public function run(): void
    {
        $dobavljaci = [
            ['naziv' => 'Hemofarm AD', 'pib' => '100000001', 'telefon' => '013/803-100', 'email' => 'nabavka@hemofarm.rs', 'aktivan' => true],
            ['naziv' => 'Galenika AD', 'pib' => '100000002', 'telefon' => '011/301-2000', 'email' => 'prodaja@galenika.rs', 'aktivan' => true],
            ['naziv' => 'Phoenix Pharma', 'pib' => '100000003', 'telefon' => '011/310-5000', 'email' => 'narudzbe@phoenix.rs', 'aktivan' => true],
            ['naziv' => 'Pharmanova', 'pib' => '100000004', 'telefon' => '011/872-1234', 'email' => 'komercijala@pharmanova.rs', 'aktivan' => true],
            ['naziv' => 'Zdravlje Leskovac', 'pib' => '100000005', 'telefon' => '016/254-111', 'email' => 'prodaja@zdravlje.rs', 'aktivan' => true],
        ];

        foreach ($dobavljaci as $dobavljac) {
            Dobavljac::create($dobavljac);
        }
    }
}
