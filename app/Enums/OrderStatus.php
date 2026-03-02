<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NACRT = 'nacrt';
    case POSLATA = 'poslata';
    case ISPORUCENA = 'isporucena';
    case OTKAZANA = 'otkazana';

    public function label(): string
    {
        return match($this) {
            self::NACRT => 'Nacrt',
            self::POSLATA => 'Poslata',
            self::ISPORUCENA => 'Isporučena',
            self::OTKAZANA => 'Otkazana',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NACRT => 'secondary',
            self::POSLATA => 'info',
            self::ISPORUCENA => 'success',
            self::OTKAZANA => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
