<?php

namespace App\Services\PlacetoPay\Deposit;

use App\Enums\DepositPaymentTypeEnum;
use App\Models\Deposit;
use App\Models\PlacetoPaySession;
use App\Models\Student;
use App\Services\PlacetoPay\AbstractPaymentHandler;
use DateTime;
use DateTimeZone;
use Dnetix\Redirection\Entities\Transaction;
use Dnetix\Redirection\Message\RedirectInformation;

/**
 * Cafeteria deposit: adds the paid amount to the student's balance.
 */
class DepositHandler extends AbstractPaymentHandler
{
    public function apply(PlacetoPaySession $session, RedirectInformation $info, ?Transaction $tx): bool
    {
        // Sessions credited before `applied_at` existed.
        if (Deposit::where('referencia', $session->reference)->exists()) {
            return true;
        }

        $student = $this->findStudent($session);

        if (! $student) {
            return true;
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

        return true;
    }

    /**
     * Take the refunded amount back from the student's balance and leave a
     * negative deposit row so the movement shows in the deposit history.
     * The balance may go negative if the money was already spent.
     */
    public function revert(PlacetoPaySession $session): void
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

    private function findStudent(PlacetoPaySession $session): ?Student
    {
        return Student::withoutGlobalScopes()->find($session->payable_id);
    }
}
