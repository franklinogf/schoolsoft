<?php

namespace App\Enums;

enum AchAccountTypeEnum: string
{
    case Saving = 's';
    case Checks = 'w';

    public function getLabel(): string
    {
        return match ($this) {
            self::Saving => 'Saving Account',
            self::Checks => 'Checks Account',
        };
    }
}
