<?php

use App\Models\PlacetoPaySession;
use App\Services\PlacetoPayPaymentProcessor;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

require_once __DIR__ . '/../../../../app.php';

/**
 * Return callback from PlacetoPay Web Checkout for store purchases. The
 * buyer's browser lands here via an external redirect, so it can arrive
 * without an active parent session. The order was already persisted as
 * unpaid by start.php and is looked up from the `placetopay_sessions` row
 * via `reference`, so we deliberately do NOT gate this page behind
 * Session::is_logged().
 *
 * Whatever the outcome (approved, rejected, cancelled, pending) the buyer
 * ends up on the payment summary page.
 */
$reference = (string) ($_GET['reference'] ?? '');
$session = $reference !== '' ? PlacetoPaySession::findByReference($reference) : null;

if (! $session || ! $session->request_id) {
    header('Location: ' . school_url('parents/options/stores/index.php?status=error&message=' . urlencode(__('placetopay.errors.session_not_found'))));
    exit;
}

$query = 'reference=' . urlencode($session->reference);

try {
    $status = (new PlacetoPayPaymentProcessor())->sync($session);

    // Paid (or still being processed), the cart has been ordered already.
    if (! $status->isRejected() && isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
} catch (PlacetoPayException $e) {
    $query .= '&error=' . urlencode($e->getMessage());
}

header('Location: ' . school_url('parents/options/placetopay/result.php?' . $query));
exit;
