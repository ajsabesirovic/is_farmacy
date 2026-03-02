<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case GOTOVINA = 'gotovina';
    case KARTICA = 'kartica';

    public function label(): string
    {
        return match($this) {
            self::GOTOVINA => 'Gotovina',
            self::KARTICA => 'Kartica',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
