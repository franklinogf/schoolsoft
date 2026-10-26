<?php

namespace App\Services;

use App\Models\Payments\AutoPay;
use App\Models\School;
use Carbon\Carbon;
use Classes\Email;

/**
 * Emails the parent about their auto pay (pre-charge reminder and failed
 * charge). The receipt of an approved charge is sent by PlacetoPay itself.
 * Messages go through the `email_queue`, sent by `cron/send-email.php`.
 */
class AutoPayNotifier
{
    /**
     * @param array<int, array{student:string, concept:string, price:float}> $items
     */
    public static function reminder(AutoPay $autoPay, array $items, Carbon $chargeDate): void
    {
        $school = School::current()->name;

        self::queue(
            $autoPay,
            __('autopay.email.reminder_subject', ['school' => $school]),
            view('emails.autopay-reminder', [
                'schoolName' => $school,
                'chargeDate' => $chargeDate->translatedFormat('d/m/Y'),
                'card' => $autoPay->cardLabel(),
                'items' => array_map(fn(array $i) => [
                    'student' => $i['student'],
                    'concept' => $i['concept'],
                    'amount' => $i['price'],
                ], $items),
                'total' => AutoPayCharges::total($items),
                'url' => self::url(),
            ]),
        );
    }

    /**
     * @param Carbon|null $nextTry null when the auto pay was stopped
     */
    public static function failed(AutoPay $autoPay, ?string $reason, ?Carbon $nextTry): void
    {
        $school = School::current()->name;

        self::queue(
            $autoPay,
            __('autopay.email.failed_subject', ['school' => $school]),
            view('emails.autopay-failed', [
                'schoolName' => $school,
                'card' => $autoPay->cardLabel(),
                'reason' => $reason,
                'nextTry' => $nextTry?->translatedFormat('d/m/Y'),
                'url' => self::url(),
            ]),
        );
    }

    private static function queue(AutoPay $autoPay, string $subject, string $html): void
    {
        if (! filter_var($autoPay->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        Email::to($autoPay->email)->subject($subject)->body($html)->queue($autoPay->cuenta);
    }

    private static function url(): string
    {
        return school_url('parents/options/autoPayments/index.php');
    }
}
