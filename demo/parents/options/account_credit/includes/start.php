<?php

use App\Models\Admin;
use App\Models\Student;
use App\Models\StoreOrder;
use App\Services\PlacetoPayCheckout;
use App\Services\PlacetoPayPaymentProcessor;
use Classes\Route;
use Classes\Session;
use Dnetix\Redirection\Exceptions\PlacetoPayException;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../../../app.php';

Session::is_logged();

if (! school_is('cbl')) {
    Route::forbidden();
}

// Route::redirect() is relative to the portal folder (/parents)
$creditUrl = '/options/account_credit/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect($creditUrl);
}

$colegio = Admin::primaryAdmin();

if ($colegio->actmen === 'No') {
    Route::redirect($creditUrl . '?status=error&message=' . urlencode(__('placetopay.account_credit.closed')));
}

if ($error = PlacetoPayPaymentProcessor::validateBuyer($_POST)) {
    Route::redirect($creditUrl . '?status=error&message=' . urlencode($error));
}

$email = trim($_POST['email'] ?? '');
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');

// Amounts are keyed by student `mt`; each one is validated against the family's own students
$amounts = is_array($_POST['amounts'] ?? null) ? $_POST['amounts'] : [];
$students = Student::byId(Session::id())->get()->keyBy('mt');

$lines = [];
$checkoutItems = [];
$total = 0;

foreach ($amounts as $mt => $rawAmount) {
    $amount = round((float) $rawAmount, 2);
    $student = $students->get($mt);

    if ($amount <= 0 || ! $student) {
        continue;
    }

    $total += $amount;

    $lines[] = [
        'item_name' => $student->ss,
        'amount' => 1,
        'size' => '',
        'price' => $amount,
    ];

    $checkoutItems[] = [
        'sku' => $student->mt,
        'name' => __('placetopay.account_credit.amount_for') . " {$student->nombre} {$student->apellidos}",
        'qty' => 1,
        'price' => $amount,
    ];
}

$total = round($total, 2);

if (empty($lines) || $total <= 0) {
    Route::redirect($creditUrl . '?status=error&message=' . urlencode(__('placetopay.account_credit.invalid')));
}

try {
    $processor = new PlacetoPayPaymentProcessor();

    if ($pending = $processor->pendingFor(Session::id())) {
        Route::redirect($creditUrl . '?status=error&message=' . urlencode(__('placetopay.errors.pending_blocked', ['reference' => $pending->reference])));
    }
} catch (PlacetoPayException $e) {
    Route::redirect($creditUrl . '?status=error&message=' . urlencode($e->getMessage()));
}

$reference = PlacetoPayCheckout::generateReference('ABO', Session::id());
$year = $colegio->year;

// Persist the order as unpaid before leaving the site, the buyer may come
// back to return.php without the session that created it.
$order = Manager::connection()->transaction(function () use ($reference, $firstName, $lastName, $email, $total, $year, $lines) {
    $order = StoreOrder::create([
        'accountID' => Session::id(),
        'trxID' => $reference,
        'customerName' => "{$firstName} {$lastName}",
        'customerEmail' => $email,
        'date' => date('Y-m-d H:i:s'),
        'subtotal' => $total,
        'ivu' => 0.00,
        'total' => $total,
        'deliveryTo' => '',
        'shopping' => 2,
        'year' => $year,
        'paid' => 0,
        'refNumber' => $reference,
    ]);

    foreach ($lines as $line) {
        $order->items()->create($line + ['year' => $year]);
    }

    return $order;
});

try {
    $session = (new PlacetoPayCheckout())->createSession([
        'reference' => $reference,
        'description' => __('placetopay.account_credit.description'),
        'amount' => $total,
        'accountId' => Session::id(),
        'buyerEmail' => $email,
        'buyerName' => $firstName,
        'buyerSurname' => $lastName,
        'buyerMobile' => PlacetoPayPaymentProcessor::normalizeMobile($_POST['mobile']),
        'payableType' => 'store_order',
        'payableId' => (string) $order->id,
        'returnUrl' => school_url('parents/options/account_credit/includes/return.php?reference=' . $reference),
        'skipResult' => true,
    ], [], $checkoutItems);

    $message = $session->statusMessage();
} catch (PlacetoPayException $e) {
    $session = null;
    $message = $e->getMessage();
}

if ($session?->process_url) {
    header('Location: ' . $session->process_url);
    exit;
}

// The checkout never started, so the unpaid order can't be paid
Manager::connection()->transaction(function () use ($order) {
    $order->items()->delete();
    $order->delete();
});

$message ??= __('placetopay.errors.start_failed');

Route::redirect($creditUrl . '?status=error&message=' . urlencode($message));
