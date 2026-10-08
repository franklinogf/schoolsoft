<?php

namespace App\Services;

use App\Enums\DepositPaymentTypeEnum;
use App\Enums\OrderPaymentTypeEnum;
use App\Enums\PlacetoPaySessionStatus;
use App\Models\Deposit;
use App\Models\PlacetoPaySession;
use App\Models\Scopes\YearScope;
use App\Models\StoreOrder;
use App\Models\Student;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Dnetix\Redirection\Entities\Transaction;
use Dnetix\Redirection\Exceptions\PlacetoPayException;
use Dnetix\Redirection\Message\RedirectInformation;
use Illuminate\Database\Capsule\Manager;

/**
 * Applies the result of a PlacetoPay session to whatever it paid for
 * (cafeteria deposit or store order). It is the single place used by the
 * buyer's return, the notification webhook and the cron probe, so every
 * step is idempotent: a payment is credited once (`applied_at`) and a
 * refund/reversal is reverted once (`refunded_at`), no matter how many
 * times or from where the session gets synced.
 */
class PlacetoPayPaymentProcessor
{
    public function __construct(private PlacetoPayCheckout $checkout = new PlacetoPayCheckout())
    {
    }

    /**
     * Query PlacetoPay for the latest status of the session and apply it.
     *
     * @throws PlacetoPayException
     */
    public function sync(PlacetoPaySession $session): PlacetoPaySessionStatus
    {
        if (! $session->request_id) {
            return $session->status;
        }

        $info = $this->checkout->querySession($session->request_id);
        $status = PlacetoPaySessionStatus::fromApiStatus($info->status()->status());

        Manager::connection()->transaction(function () use ($session, $info, $status) {
            /** @var PlacetoPaySession $locked */
            $locked = PlacetoPaySession::whereKey($session->id)->lockForUpdate()->first();

            if ($this->isRefunded($info, $status)) {
                $this->revert($locked, $info);
            } elseif ($status->isApproved()) {
                $this->apply($locked, $info);
            } elseif ($status->isRejected()) {
                $this->discard($locked);
            }
        });

        $session->refresh();

        return $session->status;
    }

    /**
     * Re-check the account's pending sessions against PlacetoPay and return
     * the first one that is still pending, if any. Used to warn the parent
     * and block a new checkout so the same thing doesn't get paid twice.
     */
    public function pendingFor(int|string $accountId): ?PlacetoPaySession
    {
        $pending = PlacetoPaySession::forAccount($accountId)->pending()->orderBy('created_at')->get();

        foreach ($pending as $session) {
            try {
                $status = $this->sync($session);
            } catch (PlacetoPayException) {
                // Can't confirm it was resolved, so treat it as still pending.
                return $session;
            }

            if ($status->isPending()) {
                return $session;
            }
        }

        return null;
    }

    /**
     * Validate the buyer data PlacetoPay requires (name, surname, email,
     * mobile) plus the terms acceptance. Names may only contain letters
     * (accents included) and spaces. Returns the first error message,
     * or null when everything is valid.
     *
     * @param array{first_name?:string, last_name?:string, email?:string, mobile?:string, terms?:string} $input
     */
    public static function validateBuyer(array $input): ?string
    {
        return match (true) {
            empty($input['terms']) => __('placetopay.validation.terms'),
            trim($input['first_name'] ?? '') === '' => __('placetopay.validation.first_name'),
            !self::isValidName($input['first_name'] ?? '') => __('placetopay.validation.first_name_chars'),
            trim($input['last_name'] ?? '') === '' => __('placetopay.validation.last_name'),
            !self::isValidName($input['last_name'] ?? '') => __('placetopay.validation.last_name_chars'),
            filter_var(trim($input['email'] ?? ''), FILTER_VALIDATE_EMAIL) === false => __('placetopay.validation.email'),
            self::normalizeMobile($input['mobile'] ?? '') === null => __('placetopay.validation.mobile'),
            default => null,
        };
    }

    /**
     * PlacetoPay rejects special characters in the buyer's name/surname:
     * only letters (any language, accents included) and spaces are allowed.
     */
    public static function isValidName(string $name): bool
    {
        return preg_match('/^[\p{L} ]+$/u', trim($name)) === 1;
    }

    /**
     * Keep only digits (and a leading +). Valid numbers have 10 to 15 digits.
     */
    public static function normalizeMobile(string $mobile): ?string
    {
        $mobile = trim($mobile);
        $digits = preg_replace('/\D/', '', $mobile);

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return (str_starts_with($mobile, '+') ? '+' : '') . $digits;
    }

    /**
     * A reversal done from the PlacetoPay console (or an ACH return) shows
     * up either as a REFUNDED request status or as a refunded transaction.
     */
    private function isRefunded(RedirectInformation $info, PlacetoPaySessionStatus $status): bool
    {
        if ($status === PlacetoPaySessionStatus::REFUNDED) {
            return true;
        }

        foreach ($info->payment() as $transaction) {
            if ($transaction->refunded() || strtoupper($transaction->status()->status()) === 'REFUNDED') {
                return true;
            }
        }

        return false;
    }

    private function apply(PlacetoPaySession $session, RedirectInformation $info): void
    {
        if ($session->applied_at) {
            return;
        }

        $tx = $info->lastApprovedTransaction() ?? $info->lastTransaction();

        match ($session->payable_type) {
            'student', Student::class => $this->creditDeposit($session, $tx),
            'store_order' => $this->markOrderPaid($session, $tx),
            default => null,
        };

        $session->update(['applied_at' => Carbon::now()]);
    }

    private function revert(PlacetoPaySession $session, RedirectInformation $info): void
    {
        if ($session->status !== PlacetoPaySessionStatus::REFUNDED) {
            $session->markStatus(PlacetoPaySessionStatus::REFUNDED, $info->toArray());
        }

        // Nothing was credited, so there is nothing to give back.
        if (! $session->applied_at || $session->refunded_at) {
            return;
        }

        match ($session->payable_type) {
            'student', Student::class => $this->refundDeposit($session),
            'store_order' => $this->findOrder($session)?->update(['paid' => 0]),
            default => null,
        };

        $session->update(['refunded_at' => Carbon::now()]);
    }

    /**
     * Cancelled/rejected checkouts will never be paid, so drop the unpaid
     * order instead of leaving it in the admin list. The attempt itself
     * stays on record in `placetopay_sessions`.
     */
    private function discard(PlacetoPaySession $session): void
    {
        if ($session->payable_type !== 'store_order' || $session->applied_at) {
            return;
        }

        $order = $this->findOrder($session);

        if ($order && ! $order->paid) {
            $order->items()->delete();
            $order->delete();
        }
    }

    private function creditDeposit(PlacetoPaySession $session, ?Transaction $tx): void
    {
        // Sessions credited before `applied_at` existed.
        if (Deposit::where('referencia', $session->reference)->exists()) {
            return;
        }

        $student = $this->findStudent($session);

        if (! $student) {
            return;
        }

        $dt = new DateTime('now', new DateTimeZone('America/Puerto_Rico'));
        $buyer = $session->last_response['request']['buyer'] ?? [];

        $student->update([
            'cantidad' => round($student->cantidad + $session->amount, 2),
        ]);

        Deposit::create([
            'id' => $student->id,
            'ss' => $student->ss,
            'cantidad' => $session->amount,
            'year' => $student->year,
            'grado' => $student->grado,
            'email' => $buyer['email'] ?? '',
            'descripcion' => "Deposito cafeteria - {$student->nombre} {$student->apellidos}",
            'fecha' => $dt->format('Y-m-d'),
            'hora' => $dt->format('H:i:s'),
            'autorizacion' => $tx?->authorization(),
            'referencia' => $session->reference,
            'tarjetaUltimosDigitos' => null,
            'studentId' => $student->mt,
            'nombreEnLaTarjeta' => trim(($buyer['name'] ?? '') . ' ' . ($buyer['surname'] ?? '')) ?: "{$student->nombre} {$student->apellidos}",
            'zip' => '',
            'tipoDePago' => DepositPaymentTypeEnum::fromPlacetoPayMethod($tx?->paymentMethod())->value,
            'otros' => trim(($tx?->franchise() ?? '') . ' ' . ($tx?->paymentMethodName() ?? '')),
            'date' => $dt->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Take the refunded amount back from the student's balance and leave a
     * negative deposit row so the movement shows in the deposit history.
     * The balance may go negative if the money was already spent.
     */
    private function refundDeposit(PlacetoPaySession $session): void
    {
        $student = $this->findStudent($session);

        if (! $student) {
            return;
        }

        $dt = new DateTime('now', new DateTimeZone('America/Puerto_Rico'));
        $original = Deposit::where('referencia', $session->reference)->first();

        $student->update([
            'cantidad' => round($student->cantidad - $session->amount, 2),
        ]);

        Deposit::create([
            'id' => $student->id,
            'ss' => $student->ss,
            'cantidad' => -$session->amount,
            'year' => $student->year,
            'grado' => $student->grado,
            'email' => $original?->email ?? '',
            'descripcion' => "Reembolso deposito cafeteria - {$student->nombre} {$student->apellidos}",
            'fecha' => $dt->format('Y-m-d'),
            'hora' => $dt->format('H:i:s'),
            'autorizacion' => $original?->autorizacion,
            'referencia' => substr($session->reference . '-R', 0, 40),
            'tarjetaUltimosDigitos' => null,
            'studentId' => $student->mt,
            'nombreEnLaTarjeta' => $original?->nombreEnLaTarjeta ?? '',
            'zip' => '',
            'tipoDePago' => $original?->tipoDePago,
            'otros' => 'Reembolso PlacetoPay',
            'date' => $dt->format('Y-m-d H:i:s'),
        ]);
    }

    private function markOrderPaid(PlacetoPaySession $session, ?Transaction $tx): void
    {
        $order = $this->findOrder($session);

        if (! $order || $order->paid) {
            return;
        }

        $order->update([
            'paid' => 1,
            'refNumber' => $session->reference,
            'payment_type' => OrderPaymentTypeEnum::fromPlacetoPayMethod($tx?->paymentMethod()),
        ]);
    }

    private function findStudent(PlacetoPaySession $session): ?Student
    {
        return Student::withoutGlobalScopes()->find($session->payable_id);
    }

    private function findOrder(PlacetoPaySession $session): ?StoreOrder
    {
        return StoreOrder::withoutGlobalScope(YearScope::class)->find($session->payable_id);
    }
}
