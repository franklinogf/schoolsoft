<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'Activo';
    case INACTIVE = 'Inactivo';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('Activo'),
            self::INACTIVE => __('Inactivo'),
        };
    }
}
