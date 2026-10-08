<?php

$isCli = PHP_SAPI === 'cli';

if ($isCli) {
    // app.php derives the school acronym from PHP_SELF, which under CLI is the
    // absolute script path; fake the web-relative one (/<school>/cron/<file>).
    $_SERVER['SCRIPT_FILENAME'] = __FILE__;
    $_SERVER['PHP_SELF'] = '/' . basename(dirname(__DIR__)) . '/cron/' . basename(__FILE__);
}

require_once __DIR__ . '/../app.php';

use App\Enums\PlacetoPaySessionStatus;
use App\Models\PlacetoPaySession;
use App\Services\PlacetoPayPaymentProcessor;
use Carbon\Carbon;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

/**
 * PlacetoPay probe (sonda): backup for the notification webhook. Re-queries
 * every session still pending, plus recently approved ones (to catch a
 * reversal whose notification got lost), and applies the result through the
 * same idempotent processor.
 *
 * Called by the hosting cron, either through PHP CLI (no token needed, shell
 * access is already trusted):
 *   php /home/<user>/domains/<domain>/public_html/<school>/cron/placetopay.php
 * or over HTTP with the per-school token:
 *   curl -s "https://<domain>/<school>/cron/placetopay.php?token=<cron_token>"
 * Testing environment: every 5-10 minutes, only while testing.
 * Production: once a day.
 */
if (! $isCli) {
    header('Content-Type: text/plain; charset=utf-8');

    $expected = (string) school_config('services.placetopay.cron_token', '');

    if ($expected === '' || ! hash_equals($expected, (string) ($_GET['token'] ?? ''))) {
        http_response_code(403);
        echo "Forbidden\n";
        exit;
    }
}

set_time_limit(0);

$pending = PlacetoPaySession::pending()->get();

$approved = PlacetoPaySession::whereNotNull('request_id')
    ->whereIn('status', [PlacetoPaySessionStatus::APPROVED, PlacetoPaySessionStatus::APPROVED_PARTIAL, PlacetoPaySessionStatus::OK])
    ->whereNull('refunded_at')
    ->where('created_at', '>=', Carbon::now()->subDays(30))
    ->get();

$processor = new PlacetoPayPaymentProcessor();
$summary = [];

foreach ($pending->concat($approved) as $session) {
    $before = $session->status;

    try {
        $after = $processor->sync($session);
        $line = "{$before->value} -> {$after->value}";
    } catch (PlacetoPayException $e) {
        $after = $before;
        $line = 'error: ' . $e->getMessage();
    }

    $summary[$after->value] = ($summary[$after->value] ?? 0) + 1;
    echo "[{$session->reference}] {$line}\n";
}

echo "\nRevisadas: " . ($pending->count() + $approved->count()) . "\n";
foreach ($summary as $status => $count) {
    echo "  {$status}: {$count}\n";
}
