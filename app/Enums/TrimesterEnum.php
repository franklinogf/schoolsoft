<?php

namespace App\Enums;

enum TrimesterEnum: string
{
    case FIRST = 'Trimestre-1';
    case SECOND = 'Trimestre-2';
    case THIRD = 'Trimestre-3';
    case FOURTH = 'Trimestre-4';
    case SUMMER = 'Verano';

    public function getLabel(): string
    {
        return match($this) {
            self::FIRST => 'Trimestre 1',
            self::SECOND => 'Trimestre 2',
            self::THIRD => 'Trimestre 3',
            self::FOURTH => 'Trimestre 4',
            self::SUMMER => 'Verano',
        };
    }
}
