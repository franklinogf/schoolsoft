<?php

use App\Models\PlacetoPaySession;
use App\Services\PlacetoPayPaymentProcessor;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

require_once __DIR__ . '/../../../../app.php';

/**
 * Return callback from PlacetoPay Web Checkout for account credit payments.
 * The buyer's browser lands here via an external redirect, so it can arrive
 * without an active parent session. The order was already persisted as
 * unpaid by start.php and is looked up from the `placetopay_sessions` row
 * via `reference`, so this page is deliberately NOT gated by login.
 */
$reference = (string) ($_GET['reference'] ?? '');
$session = $reference !== '' ? PlacetoPaySession::findByReference($reference) : null;

if (! $session || ! $session->request_id) {
    header('Location: ' . school_url('parents/options/account_credit/index.php?status=error&message=' . urlencode(__('placetopay.errors.session_not_found'))));
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
