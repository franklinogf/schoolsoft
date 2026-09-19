<?php

use App\Enums\DepositPaymentTypeEnum;
use App\Enums\PlacetoPaySessionStatus;
use App\Models\Deposit;
use App\Models\PlacetoPaySession;
use App\Models\Student;
use App\Services\PlacetoPayCheckout;
use Dnetix\Redirection\Exceptions\PlacetoPayException;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../../../app.php';

/**
 * Return callback from PlacetoPay Web Checkout. The buyer's browser lands
 * here via an external redirect, so it can arrive without an active parent
 * session (cookie lost across the round trip, session expired while paying,
 * etc). Everything needed to validate and credit the deposit is looked up
 * from the `placetopay_sessions` row via `reference`, not from $_SESSION,
 * so we deliberately do NOT gate this page behind Session::is_logged().
 */
function redirectToDeposit(string $query): never
{
    header('Location: ' . school_url('parents/options/deposit/index.php?' . $query));
    exit;
}

$reference = $_GET['reference'] ?? null;

if (! $reference) {
    redirectToDeposit('status=error&message=' . urlencode(__('No se recibio informacion del pago.')));
}

$session = PlacetoPaySession::findByReference($reference);

if (! $session || ! $session->payable_id || ! $session->request_id) {
    redirectToDeposit('status=error&message=' . urlencode(__('No se encontro la sesion de pago.')));
}

try {
    $checkout = new PlacetoPayCheckout();
    $info = $checkout->querySession($session->request_id);
} catch (PlacetoPayException $e) {
    redirectToDeposit('status=error&message=' . urlencode($e->getMessage()));
}

$status = PlacetoPaySessionStatus::fromApiStatus($info->status()->status());

$approved = in_array($status, [
    PlacetoPaySessionStatus::OK,
    PlacetoPaySessionStatus::APPROVED,
    PlacetoPaySessionStatus::APPROVED_PARTIAL,
], true);

if (! $approved) {
    redirectToDeposit('status=error&message=' . urlencode($info->status()->message()));
}

$alreadyCredited = Deposit::where('referencia', $session->reference)->exists();

if ($alreadyCredited) {
    redirectToDeposit('status=success&amount=' . urlencode(number_format($session->amount, 2)));
}

$student = Student::find($session->payable_id);

if (! $student) {
    redirectToDeposit('status=error&message=' . urlencode(__('No se encontro el estudiante del deposito.')));
}

$tx = $info->lastTransaction();

$dt = new DateTime('now', new DateTimeZone('America/Puerto_Rico'));
$newDepositAmount = number_format($student->cantidad + $session->amount, 2);

Manager::connection()->transaction(function () use ($student, $newDepositAmount, $session, $tx, $dt) {
    $student->update([
        'cantidad' => $newDepositAmount,
    ]);

    Deposit::create([
        'id' => $student->id,
        'ss' => $student->ss,
        'cantidad' => $session->amount,
        'year' => $student->year,
        'grado' => $student->grado,
        'email' => $session->last_response['payer']['email'] ?? '',
        'descripcion' => "Deposito cafeteria - {$student->nombre} {$student->apellidos}",
        'fecha' => $dt->format('Y-m-d'),
        'hora' => $dt->format('H:i:s'),
        'autorizacion' => $tx?->authorization(),
        'referencia' => $tx?->reference() ?? $session->reference,
        'tarjetaUltimosDigitos' => null,
        'studentId' => $student->mt,
        'nombreEnLaTarjeta' => trim(($session->last_response['request']['payer']['name'] ?? '') . ' ' . ($session->last_response['request']['payer']['surname'] ?? '')) ?: "{$student->nombre} {$student->apellidos}",
        'zip' => '',
        'tipoDePago' => DepositPaymentTypeEnum::fromPlacetoPayMethod($tx?->paymentMethod())->value,
        'otros' => trim(($tx?->franchise() ?? '') . ' ' . ($tx?->paymentMethodName() ?? '')),
        'date' => $dt->format('Y-m-d H:i:s'),
    ]);
});

redirectToDeposit('status=success&amount=' . urlencode($newDepositAmount));
