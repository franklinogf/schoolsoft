<?php

namespace App\Services;

use App\Models\Payments\AutoPay;
use App\Models\Student;
use Carbon\Carbon;

/**
 * What an auto pay would charge right now: for every line (student + budget
 * code) the oldest unpaid charge of the account with its current balance.
 * The amount is never frozen, so it follows the account's balances.
 */
class AutoPayCharges
{
    /**
     * @return array<int, array{sku:int|string, name:string, qty:int, price:float, student:string, concept:string}>
     */
    public static function items(AutoPay $autoPay): array
    {
        $charges = FamilyChargesService::charges($autoPay->cuenta);
        $students = Student::byId($autoPay->cuenta)->get()->keyBy('mt');
        $items = [];

        foreach ($autoPay->items as $line) {
            $student = $students->get($line->estudianteId);

            // charges() is ordered by due date, so first() is the oldest unpaid one
            $entry = $student ? $charges->first(fn(array $c) => ! $c['isPaid']
                && (string) $c['charge']->ss === (string) $student->ss
                && (int) $c['charge']->codigo === (int) $line->presupuesto) : null;

            if (! $entry) {
                continue;
            }

            $month = ucfirst(Carbon::parse($entry['charge']->fecha_d)->translatedFormat('F Y'));
            $studentName = trim("{$student->nombre} {$student->apellidos}");
            $concept = trim("{$entry['charge']->desc1} ({$month})");

            $items[] = [
                'sku' => $entry['charge']->mt,
                'name' => "{$concept} - {$studentName}",
                'qty' => 1,
                'price' => $entry['balance'],
                'student' => $studentName,
                'concept' => $concept,
            ];
        }

        return $items;
    }

    /**
     * @param array<int, array{price:float}> $items
     */
    public static function total(array $items): float
    {
        return round(array_sum(array_column($items, 'price')), 2);
    }

    /**
     * Next (or current) charge date for the auto pay's day of payment. Days
     * past the end of a short month (e.g. 30 in February) fall on its last day.
     * A date already passed this month rolls to next month.
     */
    public static function nextChargeDate(int $day, ?Carbon $from = null): Carbon
    {
        $from = ($from ?? Carbon::today())->copy()->startOfDay();
        $date = $from->copy()->startOfMonth()->day(min($day, $from->daysInMonth));

        if ($date->lt($from)) {
            $next = $from->copy()->addMonthNoOverflow()->startOfMonth();
            $date = $next->copy()->day(min($day, $next->daysInMonth));
        }

        return $date;
    }
}
