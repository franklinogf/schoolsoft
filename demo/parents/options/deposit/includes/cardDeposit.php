<?php

use App\Models\Student;
use App\Services\EvertecPayment;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}
/**
 * @var array{
 *     customerName: string,
 *     customerEmail: string,
 *     accountID: string,
 *     trxAmount: float,
 *     cardNumber: string,
 *     expDate: string,
 *     cvv: string,
 *     trxDescription: string,
 *     filler1: string
 * }|null $postData
 */
$postData = json_decode(file_get_contents('php://input'), true);
if (!$postData) {
    echo json_encode(['success' => false, 'error' => 'No payment data provided']);
    exit;
}



$evertec = new EvertecPayment();

$response =  $evertec->processCreditCard($postData);

if ($response['success'] === true) {
    $dt = new DateTime("now", new DateTimeZone("America/Puerto_Rico"));

    $studentId = $postData['filler1'];
    $depositAmount = $postData['trxAmount'];

    $student = Student::find($studentId);
    $studentMoneyAmount = $student->cantidad;

    $newDepositAmount = number_format($studentMoneyAmount + $depositAmount, 2);

    Manager::connection()->transaction(function () use ($student, $newDepositAmount, $postData, $response, $dt) {
        $fullName = $postData['customerName'];
        $customerEmail = $postData['customerEmail'];
        $account = $postData['accountID'];
        $description = $postData['trxDescription'];
        $cardLast4Digits = substr($postData['cardNumber'], -4);
        $zip = $postData['zipcode'];
        $referenceNumber = $response['refNumber'];
        $authNumber = $response['authNumber'];

        $fecha = $dt->format('Y-m-d');
        $hora = $dt->format('H:i:s');

        $student->update([
            'cantidad' => $newDepositAmount,
        ]);

        Manager::table('depositos')->insert([
            'id' => $account,
            'ss' => $student->ss,
            'cantidad' => $newDepositAmount,
            'year' => $student->year,
            'email' => $customerEmail,
            'descripcion' => $description,
            'fecha' => $fecha,
            'hora' => $hora,
            'autorizacion' => $authNumber,
            'referencia' => $referenceNumber,
            'tarjetaUltimosDigitos' => $cardLast4Digits,
            'studentId' => $student->mt,
            'nombreEnLaTarjeta' => $fullName,
            'zip' => $zip,
            'tipoDePago' => 'Tarjeta',
        ]);

        // Email::queue();
    });
    echo json_encode([...$response, "newDepositAmount" => $newDepositAmount]);
    exit;
}

echo json_encode($response);
exit;
