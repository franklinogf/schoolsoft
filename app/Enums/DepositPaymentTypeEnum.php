<?php

namespace App\Enums;

/**
 * Values stored in `depositos.tipoDePago` for cafeteria deposits.
 *
 * ACH/TARJETA/CASH are written by the online deposit flow and manual admin
 * entries going forward. The rest are historical values used by the admin
 * "deposit correction" screens (`payments/includes/deposit.php`).
 */
enum DepositPaymentTypeEnum: string
{
    case TARJETA = 'Tarjeta';
    case ACH = 'ACH';
    case CASH = 'Cash';

    case DONACION = 'Donación';
    case INTERCAMBIO_LT = 'Intercambio LT';
    case BORRAR = 'Borrar';
    case DEVOLUCION_BALANCE = 'Devolución Balance';
    case OTROS = 'Otros';
    case RECOMPENSA = 'Recompensa';
    case CORRECCION = 'Corrección';
    case CORRECCION_TARJETA = 'Corrección Tarjeta';
    case CORRECCION_ACH = 'Corrección ACH';
    case PAGO_OFICINA = 'Pago a través de oficina';
    case TRANSFERENCIA_FAMILIA = 'Transferencia en familia';

    public function label(): string
    {
        return match ($this) {
            self::TARJETA => __('Tarjeta'),
            self::ACH => __('ACH'),
            self::CASH => __('Efectivo'),
            self::DONACION => __('Donación'),
            self::INTERCAMBIO_LT => __('Intercambio LT'),
            self::BORRAR => __('Borrado'),
            self::DEVOLUCION_BALANCE => __('Devolución de Balance'),
            self::OTROS => __('Otros'),
            self::RECOMPENSA => __('Recompensa'),
            self::CORRECCION => __('Corrección'),
            self::CORRECCION_TARJETA => __('Corrección Tarjeta'),
            self::CORRECCION_ACH => __('Corrección ACH'),
            self::PAGO_OFICINA => __('Pago a través de oficina'),
            self::TRANSFERENCIA_FAMILIA => __('Transferencia en familia'),
        };
    }

    /**
     * Classifies the payment method code PlacetoPay reports on a completed
     * transaction (card network code like `visa`/`master`, or a bank-debit
     * code like `ach`/`pse`) into ACH or Tarjeta. PlacetoPay never reports
     * Cash — that value is only entered manually by an admin.
     */
    public static function fromPlacetoPayMethod(?string $paymentMethod): self
    {
        $method = strtolower($paymentMethod ?? '');

        return str_contains($method, 'ach') || str_contains($method, 'pse') || str_contains($method, 'bank')
            ? self::ACH
            : self::TARJETA;
    }
}
