<?php

namespace App\Enums;

enum PaymentType: string
{
    case Cash = 'cash';
    case Mobile = 'mobile';
    case Card = 'card';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::Mobile => 'Mobile Money',
            self::Card => 'Carte',
        };
    }
}
