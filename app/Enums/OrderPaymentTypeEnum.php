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

    /**
     * Classifies the payment method code PlacetoPay reports on a completed
     * transaction (card network code like `visa`/`master`, or a bank-debit
     * code like `ach`/`pse`) into ACH or credit card.
     */
    public static function fromPlacetoPayMethod(?string $paymentMethod): self
    {
        $method = strtolower($paymentMethod ?? '');

        return str_contains($method, 'ach') || str_contains($method, 'pse') || str_contains($method, 'bank')
            ? self::ACH
            : self::CREDIT_CARD;
    }
}
