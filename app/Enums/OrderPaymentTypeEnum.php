<?php

namespace App\Enums;

enum OrderPaymentTypeEnum: string
{
    case CREDIT_CARD = 'credit';
    case ACH = 'ach';
    case CASH = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::CREDIT_CARD => __('Tarjeta de Crédito'),
            self::ACH => __('ACH'),
            self::CASH => __('Efectivo'),
        };
    }
}
