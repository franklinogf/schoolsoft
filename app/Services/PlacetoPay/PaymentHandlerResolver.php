<?php

namespace App\Services\PlacetoPay;

use App\Models\Student;
use App\Services\PlacetoPay\Deposit\DepositHandler;
use App\Services\PlacetoPay\FamilyCharges\FamilyChargesHandler;
use App\Services\PlacetoPay\StoreOrder\StoreOrderHandler;

/**
 * Maps a session's `payable_type` to the handler that knows how to credit,
 * revert and discard it. Register new payable types here.
 */
class PaymentHandlerResolver
{
    /** @var array<string, class-string<PaymentHandler>> */
    private const HANDLERS = [
        'student' => DepositHandler::class,
        Student::class => DepositHandler::class,
        'store_order' => StoreOrderHandler::class,
        'family' => FamilyChargesHandler::class,
    ];

    public static function for(?string $payableType): ?PaymentHandler
    {
        $class = self::HANDLERS[$payableType] ?? null;

        return $class ? new $class() : null;
    }
}
