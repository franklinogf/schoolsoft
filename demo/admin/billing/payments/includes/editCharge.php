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
    $code = $_POST['code'];

    $student = Student::find($chargeTo);

    $month = date('m', strtotime($date));
    $grade = $student->grado;

    $charge = Payment::find($id);


    Payment::query()->where([
        ['id', $charge->id],
        ['baja', ''],
        ['grado', $charge->grado],
        ['codigo', $charge->codigo]
    ])
        ->whereMonth('fecha_d', $month)
        ->update([
            'nombre' => "$student->nombre $student->apellidos",
            'desc1' => $description,
            'codigo' => $code,
            'fecha_d' => $date,
            'ss' => $student->ss,
            'grado' => $grade,            
        ]);

    $charge->update([
        'nombre' => "$student->nombre $student->apellidos",
        'desc1' => $description,
        'codigo' => $code,
        'fecha_d' => $date,
        'ss' => $student->ss,
        'grado' => $grade,
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
            'code' => $charge->codigo,
            "description" => $charge->desc1,
        ];
        echo json_encode($data);
    } else {
        echo json_encode(['error' => true]);
    }
} else {
    Route::error();
}
