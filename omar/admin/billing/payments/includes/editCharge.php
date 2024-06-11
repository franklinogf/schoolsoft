<?php
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

require_once '../.././../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $chargeTo = $_POST['chargeTo'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $chargeId = $_POST['chargeId'];

    $student = new Student($chargeTo);

    $month = date('m', strtotime($date));


    DB::table('pagos')->where('mt', $id)->update([
        'nombre' => "$student->nombre $student->apellidos",
        'desc1' => $description,
        'fecha_d' => $date,
        'ss' => $student->ss,
        'grado' => $student->grado,
        'deuda' => $amount,
    ]);


    Route::redirect("/billing/payments?accountId={$student->id}&month={$month}");


} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $id = $_GET['id'];
    $charge = DB::table('pagos')->where('mt', $id)->first();
    $student = new Student($charge->ss);

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
