<?php

use App\Models\Payment;
use App\Models\Student;
use Classes\Route;
use Illuminate\Database\Capsule\Manager as DB;


require_once __DIR__ . '/../../../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $chargeTo = $_POST['chargeTo'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $chargeId = $_POST['chargeId'];

    $student = Student::find($chargeTo);

    $month = date('m', strtotime($date));


    $charge = Payment::find($id);
    $charge->update([
        'nombre' => "$student->nombre $student->apellidos",
        'desc1' => $description,
        'fecha_d' => $date,
        'ss' => $student->ss,
        'grado' => $student->grado,
        'deuda' => $amount,
    ]);


    Route::redirect("/billing/payments/index.php?accountId={$student->id}&month={$month}");
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $id = $_GET['id'];
    $charge = Payment::find($id);
    $student = $charge->student;

    if ($charge) {
        $data = [
            "id" => intval($charge->mt),
            "chargeTo" => intval($student->mt),
            "amount" => floatval($charge->deuda),
            "date" => $charge->fecha_d,
            "description" => $charge->desc1,
        ];
        echo json_encode($data);
    } else {
        echo json_encode(['error' => true]);
    }
} else {
    Route::error();
}
