<?php

namespace App\Enums;

enum DecimalTrimesterEnum:string
{
    case FIRST = 'Trimestre-1'; 
    case THIRD = 'Trimestre-3';
    case SUMMER = 'Verano';
    
    public function getLabel(): string
    {
        return match($this) {
            self::FIRST => 'Trimestre 1',
            self::THIRD => 'Trimestre 3',
            self::SUMMER => 'Verano',
        };
    }
    
}
