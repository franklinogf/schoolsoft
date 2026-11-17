<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\PlacetoPaySession;

/**
 * Lightweight status check polled by result.php while a payment is pending.
 * Only reads the stored status (updated by the webhook and the cron probe);
 * it never calls the PlacetoPay API. Exposes nothing but the status, so it
 * doesn't require login (the buyer may return without a session).
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$reference = (string) ($_GET['reference'] ?? '');
$session = $reference !== '' ? PlacetoPaySession::findByReference($reference) : null;

if (! $session) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

echo json_encode([
    'status' => $session->status->value,
    'pending' => $session->status->isPending(),
]);
