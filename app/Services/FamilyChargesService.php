<?php

namespace App\Services;

use App\Models\Payments\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds the family's charges (tuition, fees, etc.) from the `pagos` table,
 * where a charge is a row with `deuda` and its payments are rows with `pago`
 * for the same student, code and due date.
 */
class FamilyChargesService
{
    /**
     * Charges of the current year for the account, ordered by due date.
     *
     * @return Collection<int, array{charge: Payment, total: float, paid: float, balance: float, isPaid: bool, paidDate: ?string, isOverdue: bool}>
     */
    public static function charges(int|string $accountId): Collection
    {
        $today = Carbon::today()->toDateString();

        return Payment::query()
            ->where('id', $accountId)
            ->where('baja', '')
            ->get()
            ->groupBy(fn(Payment $row) => "{$row->ss}-{$row->codigo}-{$row->fecha_d}")
            ->map(function (Collection $group) use ($today) {
                $charge = $group->first(fn(Payment $row) => $row->deuda > 0);

                if (! $charge) {
                    return null;
                }

                $total = round($group->sum(fn(Payment $row) => (float) $row->deuda), 2);
                $paid = round($group->sum(fn(Payment $row) => (float) $row->pago), 2);
                $balance = round($total - $paid, 2);

                return [
                    'charge' => $charge,
                    'total' => $total,
                    'paid' => $paid,
                    'balance' => max($balance, 0.0),
                    'isPaid' => $balance <= 0.0,
                    'paidDate' => $group->filter(fn(Payment $row) => $row->pago > 0)->max('fecha_p'),
                    'isOverdue' => $balance > 0.0 && $charge->fecha_d < $today,
                ];
            })
            ->filter()
            ->sortBy(fn(array $item) => $item['charge']->fecha_d)
            ->values();
    }

    /**
     * Unpaid charges of the account among the given charge ids (`mt`).
     *
     * @param array<int|string> $chargeIds
     * @return Collection<int, array{charge: Payment, total: float, paid: float, balance: float, isPaid: bool, paidDate: ?string, isOverdue: bool}>
     */
    public static function payable(int|string $accountId, array $chargeIds): Collection
    {
        $chargeIds = array_map('intval', $chargeIds);

        return self::charges($accountId)
            ->filter(fn(array $item) => ! $item['isPaid'] && in_array((int) $item['charge']->mt, $chargeIds, true))
            ->values();
    }
}
