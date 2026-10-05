<?php

use App\Models\Admin;
use App\Models\Student;
use App\Services\PlacetoPayCheckout;
use App\Services\PlacetoPayPaymentProcessor;
use Classes\Route;
use Classes\Session;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

require_once __DIR__ . '/../../../../app.php';

Session::is_logged();

// Route::redirect() is relative to the portal folder (/parents)
$depositUrl = '/options/deposit/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect($depositUrl);
}

$studentId = $_POST['student_id'] ?? null;
$amount = round((float) ($_POST['amount'] ?? 0), 2);
$email = trim($_POST['email'] ?? '');
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');

$colegio = Admin::primaryAdmin();
$minAmount = (float) $colegio->deposito_minimo;

$student = Student::byId(Session::id())->find($studentId);

if (! $student || $amount < $minAmount) {
    Route::redirect($depositUrl . '?status=error&message=' . urlencode(__('placetopay.errors.invalid_deposit')));
}

if ($error = PlacetoPayPaymentProcessor::validateBuyer($_POST)) {
    Route::redirect($depositUrl . '?status=error&message=' . urlencode($error));
}

try {
    $processor = new PlacetoPayPaymentProcessor();

    if ($pending = $processor->pendingFor(Session::id())) {
        Route::redirect($depositUrl . '?status=error&message=' . urlencode(__('placetopay.errors.pending_blocked', ['reference' => $pending->reference])));
    }

    $reference = PlacetoPayCheckout::generateReference('DEP', $student->mt);

    $session = (new PlacetoPayCheckout())->createSession([
        'reference' => $reference,
        'description' => "Deposito cafeteria - {$student->nombre} {$student->apellidos}",
        'amount' => $amount,
        'accountId' => Session::id(),
        'buyerEmail' => $email,
        'buyerName' => $firstName,
        'buyerSurname' => $lastName,
        'buyerMobile' => PlacetoPayPaymentProcessor::normalizeMobile($_POST['mobile']),
        'payableType' => 'student',
        'payableId' => (string) $student->mt,
        'returnUrl' => school_url('parents/options/deposit/includes/return.php?reference=' . $reference),
        'skipResult' => true,
    ]);
} catch (PlacetoPayException $e) {
    Route::redirect($depositUrl . '?status=error&message=' . urlencode($e->getMessage()));
    exit;
}

if ($session->process_url) {
    header('Location: ' . $session->process_url);
    exit;
}

$message = $session->statusMessage() ?? __('placetopay.errors.start_failed');

Route::redirect($depositUrl . '?status=error&message=' . urlencode($message));
