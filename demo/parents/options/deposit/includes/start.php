<?php

use App\Models\Admin;
use App\Models\Student;
use App\Services\PlacetoPayCheckout;
use Classes\Route;
use Classes\Session;
use Dnetix\Redirection\Exceptions\PlacetoPayException;

require_once __DIR__ . '/../../../../app.php';

Session::is_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect('/parents/options/deposit/index.php');
}

$studentId = $_POST['student_id'] ?? null;
$amount = (float) ($_POST['amount'] ?? 0);
$email = trim($_POST['email'] ?? '');
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');

$colegio = Admin::primaryAdmin();
$minAmount = (float) $colegio->deposito_minimo;

$student = Student::byId(Session::id())->find($studentId);

if (! $student || $amount < $minAmount || $email === '' || $firstName === '' || $lastName === '') {
    Route::redirect('/parents/options/deposit/index.php?status=error&message=' . urlencode(__('Datos de deposito invalidos.')));
}

$reference = 'DEP_' . $student->mt . '_' . date('YmdHis') . '_' . random_int(1000, 9999);

try {
    $checkout = new PlacetoPayCheckout();

    $session = $checkout->createSession([
        'reference' => $reference,
        'description' => "Deposito cafeteria - {$student->nombre} {$student->apellidos}",
        'amount' => $amount,
        'buyerEmail' => $email,
        'buyerName' => $firstName,
        'buyerSurname' => $lastName,
        'payableType' => Student::class,
        'payableId' => (string) $student->mt,
        'returnUrl' => school_url('parents/options/deposit/includes/return.php?reference=' . $reference),
        'skipResult' => true,
    ]);
} catch (PlacetoPayException $e) {
    Route::redirect('/parents/options/deposit/index.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}

if ($session->process_url) {
    header('Location: ' . $session->process_url);
    exit;
}

$message = $session->last_response['error']
    ?? $session->last_response['status']['message']
    ?? __('No se pudo iniciar el pago.');

Route::redirect('/parents/options/deposit/index.php?status=error&message=' . urlencode($message));
