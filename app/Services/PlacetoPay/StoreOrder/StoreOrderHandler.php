<?php

namespace App\Services\PlacetoPay\StoreOrder;

use App\Enums\OrderPaymentTypeEnum;
use App\Models\PlacetoPaySession;
use App\Models\Scopes\YearScope;
use App\Models\StoreOrder;
use App\Services\PlacetoPay\AbstractPaymentHandler;
use Dnetix\Redirection\Entities\Transaction;
use Dnetix\Redirection\Message\RedirectInformation;

/**
 * Store order: the order is created unpaid before the checkout and flagged
 * as paid once PlacetoPay approves it.
 */
class StoreOrderHandler extends AbstractPaymentHandler
{
    public function apply(PlacetoPaySession $session, RedirectInformation $info, ?Transaction $tx): bool
    {
        $order = $this->findOrder($session);

        if (! $order || $order->paid) {
            return true;
        }

        $order->update([
            'paid' => 1,
            'refNumber' => $session->reference,
            'payment_type' => OrderPaymentTypeEnum::fromPlacetoPayMethod($tx?->paymentMethod()),
        ]);

        return true;
    }

    public function revert(PlacetoPaySession $session): void
    {
        $this->findOrder($session)?->update(['paid' => 0]);
    }

    /**
     * Cancelled/rejected checkouts will never be paid, so drop the unpaid
     * order instead of leaving it in the admin list. The attempt itself
     * stays on record in `placetopay_sessions`.
     */
    public function discard(PlacetoPaySession $session): void
    {
        $order = $this->findOrder($session);

        if ($order && ! $order->paid) {
            $order->items()->delete();
            $order->delete();
        }
    }

    private function findOrder(PlacetoPaySession $session): ?StoreOrder
    {
        return StoreOrder::withoutGlobalScope(YearScope::class)->find($session->payable_id);
    }
}
