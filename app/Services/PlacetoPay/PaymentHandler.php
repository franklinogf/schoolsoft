<?php

namespace App\Services\PlacetoPay;

use App\Models\PlacetoPaySession;
use Dnetix\Redirection\Entities\Transaction;
use Dnetix\Redirection\Message\RedirectInformation;

/**
 * What a PlacetoPay session pays for. One implementation per payable type;
 * `PlacetoPayPaymentProcessor` owns the idempotency guards (`applied_at`,
 * `refunded_at`) and the transaction, handlers only do the type-specific work.
 */
interface PaymentHandler
{
    /**
     * Credit the payable. Return false when nothing could be credited, so the
     * session stays unapplied and a later sync retries.
     */
    public function apply(PlacetoPaySession $session, RedirectInformation $info, ?Transaction $tx): bool;

    /**
     * Reverse what apply() did. Only called for applied, not yet refunded sessions.
     */
    public function revert(PlacetoPaySession $session): void;

    /**
     * A cancelled/rejected checkout will never be paid: clean up anything
     * created before the checkout. Only called for sessions that weren't applied.
     */
    public function discard(PlacetoPaySession $session): void;
}
