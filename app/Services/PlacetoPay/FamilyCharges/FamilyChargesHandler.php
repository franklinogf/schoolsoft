<?php

namespace App\Services\PlacetoPay\FamilyCharges;

use App\Enums\PaymentTypeEnum;
use App\Models\Payments\Payment;
use App\Models\PlacetoPaySession;
use App\Models\Scopes\YearScope;
use App\Services\PlacetoPay\AbstractPaymentHandler;
use DateTime;
use DateTimeZone;
use Dnetix\Redirection\Entities\Transaction;
use Dnetix\Redirection\Message\RedirectInformation;
use Illuminate\Database\Eloquent\Collection;

/**
 * Family charges: records a payment row in `pagos` for every charge the
 * session covers.
 */
class FamilyChargesHandler extends AbstractPaymentHandler
{
    /**
     * Record a payment row in `pagos` for every charge the session covers,
     * the same way the admin billing screen does, all under one receipt.
     * The charges come from the items sent to PlacetoPay (sku = charge `mt`,
     * price = amount paid) and the rows are tagged with the reference in
     * `trxID`, so a refund can reverse exactly those. Returns false when
     * nothing could be recorded.
     */
    public function apply(PlacetoPaySession $session, RedirectInformation $info, ?Transaction $tx): bool
    {
        if ($this->chargePayments($session)->isNotEmpty()) {
            return true;
        }

        $dt = new DateTime('now', new DateTimeZone('America/Puerto_Rico'));
        $receipt = (int) Payment::withoutGlobalScope(YearScope::class)->max('rec') + 1;
        $credited = false;

        foreach ($info->request()?->payment()?->items() ?? [] as $item) {
            $charge = Payment::withoutGlobalScope(YearScope::class)->find((int) $item->sku());

            if (! $charge || (string) $charge->id !== (string) $session->account_id) {
                continue;
            }

            $this->recordChargeMovement($charge, (float) $item->price() * max((int) $item->qty(), 1), [
                'tdp' => PaymentTypeEnum::fromPlacetoPayMethod($tx?->paymentMethod())->value,
                'nuchk' => substr((string) ($tx?->authorization() ?? ''), 0, 10),
                'rec' => $receipt,
                'id2' => $charge->mt,
                'trxID' => $session->reference,
            ], $dt);

            $this->setCoursePaid($charge, 'OK');
            $credited = true;
        }

        return $credited;
    }

    /**
     * Reverse each payment row created by apply() with a negative one, so the
     * charge shows as pending again and the movement stays on record.
     */
    public function revert(PlacetoPaySession $session): void
    {
        $dt = new DateTime('now', new DateTimeZone('America/Puerto_Rico'));

        foreach ($this->chargePayments($session) as $payment) {
            $this->recordChargeMovement($payment, -(float) $payment->pago, [
                'tdp' => $payment->tdp,
                'nuchk' => 'Reembolso',
                'rec' => $payment->rec,
                'id2' => $payment->id2,
                'trxID' => $session->reference . '-R',
            ], $dt);

            $this->setCoursePaid($payment, '');
        }
    }

    /**
     * Payment rows recorded in `pagos` for the session.
     *
     * @return Collection<int, Payment>
     */
    private function chargePayments(PlacetoPaySession $session): Collection
    {
        return Payment::withoutGlobalScope(YearScope::class)
            ->where('trxID', $session->reference)
            ->where('pago', '>', 0)
            ->get();
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function recordChargeMovement(Payment $charge, float $amount, array $attributes, DateTime $dt): void
    {
        Payment::create([
            'id' => $charge->id,
            'nombre' => $charge->nombre,
            'desc1' => $charge->desc1,
            'fecha_d' => $charge->fecha_d,
            'year' => $charge->year,
            'codigo' => $charge->codigo,
            'ss' => $charge->ss,
            'pago_t' => $charge->codigo == '705' ? 'OK' : '',
            'grado' => $charge->grado,
            'fecha_p' => $dt->format('Y-m-d'),
            'pago' => $amount,
            'usuario' => 'PlacetoPay',
            'hora' => $dt->format('H:i:s'),
            'fecha2' => $dt->format('Y-m-d'),
            ...$attributes,
        ]);
    }

    /**
     * Code 705 rows carry a course-paid flag (`pago_t`), kept in sync like
     * the admin billing screen does.
     */
    private function setCoursePaid(Payment $charge, string $flag): void
    {
        if ($charge->codigo != '705') {
            return;
        }

        Payment::withoutGlobalScope(YearScope::class)
            ->where('ss', $charge->ss)
            ->where('fecha_d', $charge->fecha_d)
            ->where('codigo', $charge->codigo)
            ->where('year', $charge->year)
            ->update(['pago_t' => $flag]);
    }
}
