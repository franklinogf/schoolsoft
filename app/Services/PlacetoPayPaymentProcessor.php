<?php

namespace App\Services;

use App\Enums\PlacetoPaySessionStatus;
use App\Models\PlacetoPaySession;
use App\Services\PlacetoPay\PaymentHandlerResolver;
use Carbon\Carbon;
use Dnetix\Redirection\Exceptions\PlacetoPayException;
use Dnetix\Redirection\Message\RedirectInformation;
use Illuminate\Database\Capsule\Manager;

/**
 * Applies the result of a PlacetoPay session to whatever it paid for
 * (cafeteria deposit, store order or family charges). It is the single place used by the
 * buyer's return, the notification webhook and the cron probe, so every
 * step is idempotent: a payment is credited once (`applied_at`) and a
 * refund/reversal is reverted once (`refunded_at`), no matter how many
 * times or from where the session gets synced.
 *
 * The type-specific work lives in one handler per payable type
 * (see `App\Services\PlacetoPay\PaymentHandlerResolver`).
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

        $credited = PaymentHandlerResolver::for($session->payable_type)?->apply($session, $info, $tx);

        // Nothing could be credited: leave it unapplied so a later sync retries
        if ($credited === false) {
            return;
        }

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

        PaymentHandlerResolver::for($session->payable_type)?->revert($session);

        $session->update(['refunded_at' => Carbon::now()]);
    }

    private function discard(PlacetoPaySession $session): void
    {
        if ($session->applied_at) {
            return;
        }

        PaymentHandlerResolver::for($session->payable_type)?->discard($session);
    }
}
