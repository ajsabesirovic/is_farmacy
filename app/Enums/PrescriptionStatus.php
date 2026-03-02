<?php

namespace App\Enums;

enum PrescriptionStatus: string
{
    case IZDAT = 'izdat';
    case REALIZOVAN = 'realizovan';
    case ISTEKAO = 'istekao';

    public function label(): string
    {
        return match($this) {
            self::IZDAT => 'Izdat',
            self::REALIZOVAN => 'Realizovan',
            self::ISTEKAO => 'Istekao',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::IZDAT => 'success',
            self::REALIZOVAN => 'primary',
            self::ISTEKAO => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
