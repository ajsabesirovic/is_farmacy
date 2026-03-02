<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ApotekaSeeder::class,
            LekSeeder::class,
            DobavljacSeeder::class,
            LekarSeeder::class,
            KorisnikSeeder::class,
            ZalihaSeeder::class,
            DobavljacLekSeeder::class,
        ]);
    }
}
