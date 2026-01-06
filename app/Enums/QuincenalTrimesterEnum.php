<?php

namespace App\Enums;

enum QuincenalTrimesterEnum:string
{
    case FIRST_S1 = 'Quincena-1-S1';
    case SECOND_S1 = 'Quincena-2-S1';
    case THIRD_S1 = 'Quincena-3-S1';
    case FOURTH_S1 = 'Quincena-4-S1';
    case FINAL_S1 = 'Final-1';
    case FIRST_S2 = 'Quincena-1-S2';
    case SECOND_S2 = 'Quincena-2-S2';
    case THIRD_S2 = 'Quincena-3-S2';
    case FOURTH_S2 = 'Quincena-4-S2';
    case FINAL_S2 = 'Final-2';

    public function getLabel(): string
    {
        return match($this) {
            self::FIRST_S1 => 'Quincena 1 - Semestre 1',
            self::SECOND_S1 => 'Quincena 2 - Semestre 1',
            self::THIRD_S1 => 'Quincena 3 - Semestre 1',
            self::FOURTH_S1 => 'Quincena 4 - Semestre 1',
            self::FINAL_S1 => 'Final - Semestre 1',
            self::FIRST_S2 => 'Quincena 1 - Semestre 2',
            self::SECOND_S2 => 'Quincena 2 - Semestre 2',
            self::THIRD_S2 => 'Quincena 3 - Semestre 2',
            self::FOURTH_S2 => 'Quincena 4 - Semestre 2',
            self::FINAL_S2 => 'Final - Semestre 2',
        };
    }
}
