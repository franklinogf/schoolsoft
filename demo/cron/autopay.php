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
use App\Models\Admin;
use App\Models\Payments\AutoPay;
use App\Models\PlacetoPaySession;
use App\Services\AutoPayCharges;
use App\Services\AutoPayNotifier;
use App\Services\PlacetoPayCheckout;
use App\Services\PlacetoPayPaymentProcessor;
use Carbon\Carbon;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

/**
 * Auto pay job, run once a day by the hosting cron (same as placetopay.php):
 *   php /home/<user>/domains/<domain>/public_html/<school>/cron/autopay.php
 *   curl -s "https://<domain>/<school>/cron/autopay.php?token=<cron_token>"
 *
 * 1. Reminder: 3 days before the charge date the parent is emailed the
 *    estimated amount (once per charge date).
 * 2. Charge: active auto pays whose payment day arrived and weren't charged
 *    this month are charged with the stored PlacetoPay token (collect()). A
 *    rejected charge sets `proximo_intento` (see AutoPayHandler::discard) and
 *    is retried by this same job, so retries need nothing extra.
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

const REMINDER_DAYS_BEFORE = 3;

$today = Carbon::today();
$monthStart = $today->copy()->startOfMonth();
// In short months, days past the last one (e.g. 30 in February) are charged on the last day.
$dayLimit = $today->isLastOfMonth() ? 31 : $today->day;

$year = Admin::primaryAdmin()->year;
$checkout = new PlacetoPayCheckout();
$processor = new PlacetoPayPaymentProcessor($checkout);

$active = fn() => AutoPay::query()
    ->where('year', $year)
    ->where('estado', AutoPay::STATUS_ACTIVE)
    ->where('formaDePago', 'automatico')
    ->whereNotNull('pp_token')
    ->with('items');

/* --------------------------------- Reminder -------------------------------- */

foreach ($active()->get() as $autoPay) {
    $chargeDate = AutoPayCharges::nextChargeDate((int) $autoPay->diaDePago, $today);
    $chargedThisCycle = $autoPay->ultimo_cobro && Carbon::parse($autoPay->ultimo_cobro)->gte($monthStart)
        && $chargeDate->isSameMonth($today);
    $due = $today->gte($chargeDate->copy()->subDays(REMINDER_DAYS_BEFORE)) && $today->lt($chargeDate);

    if (! $due || $chargedThisCycle || $autoPay->aviso_enviado === $chargeDate->toDateString()) {
        continue;
    }

    $items = AutoPayCharges::items($autoPay);

    if ($items) {
        AutoPayNotifier::reminder($autoPay, $items, $chargeDate);
        $autoPay->update(['aviso_enviado' => $chargeDate->toDateString()]);
        echo "[posteo {$autoPay->id}] aviso previo para {$chargeDate->toDateString()}\n";
    }
}

/* ---------------------------------- Charge --------------------------------- */

$autoPays = $active()
    ->where('diaDePago', '<=', $dayLimit)
    ->where(fn($q) => $q->whereNull('ultimo_cobro')->orWhere('ultimo_cobro', '<', $monthStart->toDateString()))
    ->where(fn($q) => $q->whereNull('proximo_intento')->orWhere('proximo_intento', '<=', $today->toDateString()))
    ->get();

foreach ($autoPays as $autoPay) {
    $label = "[posteo {$autoPay->id} / cuenta {$autoPay->cuenta}]";

    // A charge already in flight or approved this month (e.g. waiting for the sync) is not repeated;
    // rejected ones are, that's what the retries are.
    $inFlight = PlacetoPaySession::where('payable_type', 'autopay')
        ->where('payable_id', (string) $autoPay->id)
        ->where('amount', '>', 0)
        ->where('created_at', '>=', $monthStart)
        ->whereIn('status', [
            PlacetoPaySessionStatus::PENDING,
            PlacetoPaySessionStatus::PENDING_VALIDATION,
            PlacetoPaySessionStatus::APPROVED,
            PlacetoPaySessionStatus::APPROVED_PARTIAL,
            PlacetoPaySessionStatus::OK,
        ])
        ->exists();

    if ($inFlight) {
        echo "{$label} ya cobrado o en proceso este mes\n";
        continue;
    }

    $items = AutoPayCharges::items($autoPay);

    if (! $items) {
        echo "{$label} sin balances pendientes\n";
        continue;
    }

    try {
        $session = $checkout->collect([
            'reference' => PlacetoPayCheckout::generateReference('AUT', $autoPay->cuenta),
            'description' => 'Pago automatico - Cuenta ' . $autoPay->cuenta,
            'amount' => AutoPayCharges::total($items),
            'accountId' => $autoPay->cuenta,
            'payableType' => 'autopay',
            'payableId' => (string) $autoPay->id,
            'buyerEmail' => $autoPay->email,
            'buyerName' => (string) $autoPay->pp_nombre,
            'buyerSurname' => (string) $autoPay->pp_apellido,
        ], ['token' => $autoPay->pp_token, 'subtoken' => $autoPay->pp_subtoken], $items);

        $status = $processor->sync($session);
        echo "{$label} {$session->reference}: {$status->value}\n";
    } catch (PlacetoPayException $e) {
        // Technical failure (no answer from PlacetoPay): nothing was charged, tomorrow's run tries again.
        echo "{$label} error: " . $e->getMessage() . "\n";
    }
}

echo "\nRevisados: {$autoPays->count()}\n";
