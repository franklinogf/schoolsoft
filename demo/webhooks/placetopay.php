<?php

require_once __DIR__ . '/../app.php';

use App\Models\PlacetoPaySession;
use App\Services\PlacetoPayCheckout;
use App\Services\PlacetoPayPaymentProcessor;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

/**
 * PlacetoPay notification webhook (register in the PlacetoPay console as
 * https://<domain>/<school>/webhooks/placetopay.php).
 *
 * PlacetoPay POSTs a JSON body { status, requestId, reference, signature }
 * every time a session changes state: approvals, rejections, ACH validations
 * and refunds/reversals (console reversals and ACH returns). The signature is
 * validated and then the session is re-queried and applied through the same
 * idempotent processor used by the buyer's return and the cron probe, so the
 * payload itself is never trusted beyond identifying the session.
 */
function respond(int $code, array $body): never
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($body);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['error' => 'Method not allowed']);
}

$payload = json_decode(file_get_contents('php://input'), true);

if (! is_array($payload) || ! isset($payload['requestId'], $payload['reference'], $payload['signature'], $payload['status']['status'])) {
    respond(400, ['error' => 'Invalid payload']);
}

$checkout = new PlacetoPayCheckout();
$notification = $checkout->readNotification($payload);

if (! $checkout->isValidNotification($notification)) {
    respond(400, ['error' => 'Invalid signature']);
}

$session = PlacetoPaySession::findByRequestId((int) $notification->requestId());

if (! $session || $session->reference !== $notification->reference()) {
    // Not ours (or another tenant's); acknowledge so it isn't retried forever.
    respond(200, ['status' => 'ignored']);
}

try {
    $status = (new PlacetoPayPaymentProcessor($checkout))->sync($session);
} catch (PlacetoPayException $e) {
    // Let PlacetoPay retry later; the cron probe is the fallback.
    respond(500, ['error' => 'Could not query the session']);
}

respond(200, ['status' => $status->value]);
