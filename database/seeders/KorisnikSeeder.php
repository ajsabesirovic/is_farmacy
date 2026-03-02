<?php

namespace Database\Seeders;

use App\Models\Korisnik;
use App\Models\Farmaceut;
use App\Models\AdminApoteke;
use App\Models\CentralniAdmin;
use App\Models\RegistrovaniKorisnik;
use App\Enums\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KorisnikSeeder extends Seeder
{
    public function run(): void
    {
        // Centralni admin
        $admin = Korisnik::create([
            'ime' => 'Admin',
            'prezime' => 'Sistem',
            'email' => 'admin@apoteke.rs',
            'lozinka_hash' => Hash::make('password'),
            'aktivan' => true,
            'tip' => UserType::CENTRALNI_ADMIN,
            'apoteka_id' => null,
        ]);
        CentralniAdmin::create([
            'id' => $admin->id,
        ]);

        // Registrovani korisnik
        $regKorisnik = Korisnik::create([
            'ime' => 'Ajsa',
            'prezime' => 'Besirovic',
            'email' => 'korisnik@apoteke.rs',
            'lozinka_hash' => Hash::make('password'),
            'aktivan' => true,
            'tip' => UserType::REGISTROVANI_KORISNIK,
            'apoteka_id' => null,
        ]);
        RegistrovaniKorisnik::create([
            'id' => $regKorisnik->id,
            'jmbg' => '0101000000001',
        ]);

        // Admini i farmaceuti za svaku apoteku
        for ($apotekaId = 1; $apotekaId <= 5; $apotekaId++) {
            // Admin apoteke
            $adminApoteke = Korisnik::create([
                'ime' => 'Admin',
                'prezime' => "Apoteka{$apotekaId}",
                'email' => "admin.apoteka{$apotekaId}@apoteke.rs",
                'lozinka_hash' => Hash::make('password'),
                'aktivan' => true,
                'tip' => UserType::ADMIN_APOTEKE,
                'apoteka_id' => $apotekaId,
            ]);
            AdminApoteke::create([
                'id' => $adminApoteke->id,
            ]);

            // 3 farmaceuta po apoteci
            for ($f = 1; $f <= 3; $f++) {
                $farmaceut = Korisnik::create([
                    'ime' => "Farmaceut{$f}",
                    'prezime' => "Apoteka{$apotekaId}",
                    'email' => "farmaceut{$f}.apoteka{$apotekaId}@apoteke.rs",
                    'lozinka_hash' => Hash::make('password'),
                    'aktivan' => true,
                    'tip' => UserType::FARMACEUT,
                    'apoteka_id' => $apotekaId,
                ]);
                Farmaceut::create([
                    'id' => $farmaceut->id,
                    'licenca' => 'LIC' . str_pad($apotekaId, 2, '0', STR_PAD_LEFT) . str_pad($f, 3, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }
}
