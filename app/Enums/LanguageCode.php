<?php

namespace App\Enums;

enum LanguageCode: string
{
    case SPANISH = 'es';
    case ENGLISH = 'en';

    public function label(): string
    {
        return match ($this) {
            self::SPANISH => __('Español'),
            self::ENGLISH => __('English'),
        };
    }
}
