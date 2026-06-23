<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentMemberEnum: string
{
    case Father = 'father';
    case Mother = 'mother';
    case Guardian = 'guardian';

    public function getLabel(): string
    {
        return match ($this) {
            self::Father => __('Padre'),
            self::Mother => __('Madre'),
            self::Guardian => __('Encargado'),
        };
    }
}
