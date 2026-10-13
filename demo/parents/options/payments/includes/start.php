<?php

use App\Models\Student;
use App\Services\FamilyChargesService;
use App\Services\PlacetoPayCheckout;
use App\Services\PlacetoPayPaymentProcessor;
use Carbon\Carbon;
use Classes\Route;
use Classes\Session;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

require_once __DIR__ . '/../../../../app.php';

Session::is_logged();

// Route::redirect() is relative to the portal folder (/parents)
$paymentsUrl = '/options/payments/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect($paymentsUrl);
}

$email = trim($_POST['email'] ?? '');
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$chargeIds = array_filter((array) ($_POST['charges'] ?? []), 'is_numeric');

if ($error = PlacetoPayPaymentProcessor::validateBuyer($_POST)) {
    Route::redirect($paymentsUrl . '?status=error&message=' . urlencode($error));
}

// Recalculate the balances server side, never trust the client amount
$charges = FamilyChargesService::payable(Session::id(), $chargeIds);

if ($charges->isEmpty() || $charges->count() !== count(array_unique($chargeIds))) {
    Route::redirect($paymentsUrl . '?status=error&message=' . urlencode(__('placetopay.errors.invalid_charges')));
}

$students = Student::byId(Session::id())->get()->keyBy('ss');
$total = round($charges->sum('balance'), 2);
// The processor credits each item back to its charge (sku = `mt`, price = amount)
$checkoutItems = [];

foreach ($charges as $item) {
    $charge = $item['charge'];
    $student = $students->get($charge->ss);
    $month = ucfirst(Carbon::parse($charge->fecha_d)->translatedFormat('F Y'));

    $checkoutItems[] = [
        'sku' => $charge->mt,
        'name' => trim("{$charge->desc1} ({$month})" . ($student ? " - {$student->nombre} {$student->apellidos}" : '')),
        'qty' => 1,
        'price' => $item['balance'],
    ];
}

try {
    $processor = new PlacetoPayPaymentProcessor();

    if ($pending = $processor->pendingFor(Session::id())) {
        Route::redirect($paymentsUrl . '?status=error&message=' . urlencode(__('placetopay.errors.pending_blocked', ['reference' => $pending->reference])));
    }

    $reference = PlacetoPayCheckout::generateReference('PAG', Session::id());

    $session = (new PlacetoPayCheckout())->createSession([
        'reference' => $reference,
        'description' => 'Pago de cargos - Cuenta ' . Session::id(),
        'amount' => $total,
        'accountId' => Session::id(),
        'buyerEmail' => $email,
        'buyerName' => $firstName,
        'buyerSurname' => $lastName,
        'buyerMobile' => PlacetoPayPaymentProcessor::normalizeMobile($_POST['mobile']),
        'payableType' => 'family',
        'payableId' => (string) Session::id(),
        'returnUrl' => school_url('parents/options/payments/includes/return.php?reference=' . $reference),
        'skipResult' => true,
    ], [], $checkoutItems);
} catch (PlacetoPayException $e) {
    Route::redirect($paymentsUrl . '?status=error&message=' . urlencode($e->getMessage()));
    exit;
}

if ($session->process_url) {
    header('Location: ' . $session->process_url);
    exit;
}

$message = $session->statusMessage() ?? __('placetopay.errors.start_failed');

Route::redirect($paymentsUrl . '?status=error&message=' . urlencode($message));
