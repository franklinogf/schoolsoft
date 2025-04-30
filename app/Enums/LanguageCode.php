<?php

namespace App\Enums;

enum LanguageCode: string
{
    case SPANISH = 'ES';
    case ENGLISH = 'EN';

    public function label(): string
    {
        return match ($this) {
            self::SPANISH => __('Español'),
            self::ENGLISH => __('English'),
        };
    }
}
