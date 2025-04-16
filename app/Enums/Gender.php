<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'M';
    case FEMALE = 'F';

    public function label(): string
    {
        return match ($this) {
            self::MALE => __('Masculino'),
            self::FEMALE => __('Femenino'),
        };
    }
}
