<?php

use App\Models\PlacetoPaySession;
use App\Services\PlacetoPayPaymentProcessor;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

require_once __DIR__ . '/../../../../app.php';

/**
 * Return callback from PlacetoPay Web Checkout. The buyer's browser lands
 * here via an external redirect, so it can arrive without an active parent
 * session (cookie lost across the round trip, session expired while paying,
 * etc). Everything needed to validate and credit the deposit is looked up
 * from the `placetopay_sessions` row via `reference`, not from $_SESSION,
 * so we deliberately do NOT gate this page behind Session::is_logged().
 *
 * Whatever the outcome (approved, rejected, cancelled, pending) the buyer
 * ends up on the payment summary page.
 */
$reference = (string) ($_GET['reference'] ?? '');
$session = $reference !== '' ? PlacetoPaySession::findByReference($reference) : null;

if (! $session || ! $session->request_id) {
    header('Location: ' . school_url('parents/options/deposit/index.php?status=error&message=' . urlencode(__('placetopay.errors.session_not_found'))));
    exit;
}

$query = 'reference=' . urlencode($session->reference);

try {
    (new PlacetoPayPaymentProcessor())->sync($session);
} catch (PlacetoPayException $e) {
    $query .= '&error=' . urlencode($e->getMessage());
}

header('Location: ' . school_url('parents/options/placetopay/result.php?' . $query));
exit;
