<?php

namespace App\Enums;

enum AutoPayPaymentEnum: string
{
    case Manual = 'manual';
    case Automatic = 'automatico';

    public function getLabel(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::Automatic => 'Automático',
        };
    }
}
