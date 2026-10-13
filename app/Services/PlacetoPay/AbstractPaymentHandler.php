<?php

namespace App\Services\PlacetoPay;

use App\Models\PlacetoPaySession;

abstract class AbstractPaymentHandler implements PaymentHandler
{
    /**
     * Most payables are only created once paid, so there is nothing to clean up.
     */
    public function discard(PlacetoPaySession $session): void
    {
    }
}
