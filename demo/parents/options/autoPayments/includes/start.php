<?php

use App\Models\Admin;
use App\Models\Payments\AutoPay;
use App\Services\PlacetoPayCheckout;
use App\Services\PlacetoPayPaymentProcessor;
use Classes\Route;
use Classes\Session;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

require_once __DIR__ . '/../../../../app.php';

Session::is_logged();

// Route::redirect() is relative to the portal folder (/parents)
$autoPaysUrl = '/options/autoPayments/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect($autoPaysUrl);
}

$autoPay = AutoPay::query()
    ->where('id', (int) ($_POST['autoPayId'] ?? 0))
    ->where('cuenta', (int) Session::id())
    ->where('year', Admin::primaryAdmin()->year)
    ->first();

if (! $autoPay) {
    Route::redirect($autoPaysUrl . '?status=error&message=' . urlencode(__('placetopay.errors.session_not_found')));
}

$_POST['email'] = $autoPay->email;

if ($error = PlacetoPayPaymentProcessor::validateBuyer($_POST)) {
    Route::redirect($autoPaysUrl . '?status=error&message=' . urlencode($error));
}

$firstName = trim($_POST['first_name']);
$lastName = trim($_POST['last_name']);

try {
    // No pendingFor() check: registering a card charges nothing, so an abandoned
    // enrollment must not block a new one (nor a pending payment, which can't be paid twice here).
    $reference = PlacetoPayCheckout::generateReference('SUB', Session::id());

    // Subscription session: PlacetoPay only stores the card and returns its token, nothing is charged.
    $session = (new PlacetoPayCheckout())->createSession([
        'reference' => $reference,
        'description' => 'Registro de tarjeta - Cuenta ' . Session::id(),
        'amount' => 0,
        'subscription' => true,
        'accountId' => Session::id(),
        'buyerEmail' => $autoPay->email,
        'buyerName' => $firstName,
        'buyerSurname' => $lastName,
        'buyerMobile' => PlacetoPayPaymentProcessor::normalizeMobile($_POST['mobile']),
        'payableType' => 'autopay',
        'payableId' => (string) $autoPay->id,
        'returnUrl' => school_url('parents/options/autoPayments/includes/return.php?reference=' . $reference),
        'skipResult' => true,
    ]);
} catch (PlacetoPayException $e) {
    Route::redirect($autoPaysUrl . '?status=error&message=' . urlencode($e->getMessage()));
    exit;
}

// The payer name is needed later by collect()
$autoPay->update(['pp_nombre' => $firstName, 'pp_apellido' => $lastName]);

if ($session->process_url) {
    header('Location: ' . $session->process_url);
    exit;
}

Route::redirect($autoPaysUrl . '?status=error&message=' . urlencode($session->statusMessage() ?? __('placetopay.errors.start_failed')));
