<?php

namespace App\Enums;

enum UserType: string
{
    case FARMACEUT = 'F';
    case ADMIN_APOTEKE = 'A';
    case CENTRALNI_ADMIN = 'C';
    case REGISTROVANI_KORISNIK = 'R';

    public function label(): string
    {
        return match($this) {
            self::FARMACEUT => 'Farmaceut',
            self::ADMIN_APOTEKE => 'Administrator apoteke',
            self::CENTRALNI_ADMIN => 'Centralni administrator',
            self::REGISTROVANI_KORISNIK => 'Registrovani korisnik',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
