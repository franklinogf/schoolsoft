<?php

namespace App\Enums;

enum AutoPayTypeEnum: string
{
    case Card = 'tarjeta';
    case Ach = 'ach';

    public function getLabel(): string
    {
        return match ($this) {
            self::Card => 'Tarjeta',
            self::Ach => 'ACH',
        };
    }
}
