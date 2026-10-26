<?php

namespace App\Services\PlacetoPay\AutoPay;

use App\Models\Payments\AutoPay;
use App\Models\Payments\AutoPayHistory;
use App\Models\PlacetoPaySession;
use App\Services\AutoPayNotifier;
use App\Services\PlacetoPay\FamilyCharges\FamilyChargesHandler;
use App\Services\PlacetoPayCheckout;
use Carbon\Carbon;
use Dnetix\Redirection\Entities\Transaction;
use Dnetix\Redirection\Message\RedirectInformation;

/**
 * Handles both kinds of session an auto pay (`posteos` row, payable_id) has:
 * - enrollment (subscription, $0): no payment, stores the card token;
 * - monthly charge (collect): records the payments like family charges and
 *   leaves a trace in `posteos_historial`.
 */
class AutoPayHandler extends FamilyChargesHandler
{
    /**
     * Rejection reasons worth retrying the next day (funds, limits, issuer
     * unavailable). Anything else (expired, lost, invalid card...) stops the
     * auto pay right away. ISO 8583 response codes as PlacetoPay returns them
     * in `payment[].status.reason`; adjust with the codes seen in production.
     */
    public const array RETRYABLE_REASONS = ['51', '61', '65', '91', '96'];

    public function apply(PlacetoPaySession $session, RedirectInformation $info, ?Transaction $tx): bool
    {
        $autoPay = AutoPay::query()->find($session->payable_id);

        if (! $autoPay) {
            return false;
        }

        // Enrollments are $0 sessions. Don't rely on `subscription`: a collect
        // response carries it too, which would be mistaken for an enrollment.
        if ($session->amount <= 0) {
            return $this->storeCard($autoPay, $info);
        }

        if (! parent::apply($session, $info, $tx)) {
            return false;
        }

        $autoPay->update([
            'ultimo_cobro' => Carbon::today()->toDateString(),
            'intentos' => 0,
            'proximo_intento' => null,
        ]);

        $this->log($autoPay, $session, 'Aprobado', substr((string) ($tx?->authorization() ?? ''), 0, 20));

        return true;
    }

    public function revert(PlacetoPaySession $session): void
    {
        if ($session->amount > 0) {
            parent::revert($session);
        }
    }

    /**
     * A rejected enrollment leaves the auto pay without card. A rejected
     * charge is retried the next day when the reason allows it, up to
     * MAX_ATTEMPTS; otherwise the auto pay is stopped. The parent is warned
     * either way. Runs once per session (guarded by its history row), since
     * the return page, the webhook and the cron can all sync the same one.
     */
    public function discard(PlacetoPaySession $session): void
    {
        $autoPay = AutoPay::query()->find($session->payable_id);

        if (! $autoPay || $session->amount <= 0) {
            return;
        }

        if (AutoPayHistory::query()->where('posteoId', $autoPay->id)->where('referenceNumber', $session->reference)->exists()) {
            return;
        }

        [$reason, $message] = $this->rejection($session);

        $attempts = $autoPay->intentos + 1;
        $retry = in_array($reason, self::RETRYABLE_REASONS, true) && $attempts < AutoPay::MAX_ATTEMPTS;
        $nextTry = $retry ? Carbon::tomorrow() : null;

        $autoPay->update($retry
            ? ['intentos' => $attempts, 'proximo_intento' => $nextTry->toDateString()]
            : ['intentos' => $attempts, 'proximo_intento' => null, 'estado' => AutoPay::STATUS_FAILED]);

        $this->log($autoPay, $session, 'Rechazado' . ($retry ? " (intento {$attempts}/" . AutoPay::MAX_ATTEMPTS . ')' : '') . ": {$message}");

        AutoPayNotifier::failed($autoPay, $message, $nextTry);
    }

    /**
     * Reason code and message PlacetoPay gave to the last transaction.
     *
     * @return array{0:string, 1:string}
     */
    private function rejection(PlacetoPaySession $session): array
    {
        $payments = $session->last_response['payment'] ?? [];
        $status = (end($payments) ?: [])['status'] ?? $session->last_response['status'] ?? [];

        return [(string) ($status['reason'] ?? ''), (string) ($status['message'] ?? '')];
    }

    private function log(AutoPay $autoPay, PlacetoPaySession $session, string $message, string $authNumber = ''): void
    {
        AutoPayHistory::create([
            'posteoId' => $autoPay->id,
            'mensaje' => $message,
            'mes' => Carbon::today()->format('m'),
            'descripcion' => $session->description,
            'dateTime' => Carbon::now(),
            'authNumber' => $authNumber,
            'referenceNumber' => $session->reference,
        ]);
    }

    private function storeCard(AutoPay $autoPay, RedirectInformation $info): bool
    {
        $token = (new PlacetoPayCheckout())->extractToken($info);

        if (! $token) {
            return false;
        }

        $autoPay->storeCardToken($token['token'], $token['subtoken'], $token['last4'], $token['brand']);

        return true;
    }
}
