<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Payment;
use App\Models\Student;
use Classes\Route;
use Illuminate\Database\Capsule\Manager as DB;


if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $school = Admin::primaryAdmin();
    $year = $school->year();
    $id = $_POST["id"];
    $bash = $_POST["bash"];
    $chargeDate = $_POST["charge_date"];
    $paymentDate = $_POST["payment_date"];
    $chargeTo = $_POST["chargeTo"];
    $description = $_POST["description"];
    $amount = $_POST["amount"];
    $paymentType = $_POST["paymentType"];
    $chkNum = $_POST["chkNum"];
    $comment = $_POST["comment"];

    $student = Student::find($chargeTo);

    $month = date('m', strtotime($chargeDate));
    $data = [
        'nombre' => "$student->nombre $student->apellidos",
        'ss' => $student->ss,
        'grado' => $student->grado,
        'desc1' => $description,
        'fecha_d' => $chargeDate,
        'pago' => $amount,
        "bash" => $bash,
        "fecha_p" => $paymentDate,
        "tdp" => $paymentType,
        'nuchk' => $chkNum,
        'razon' => $comment,
        'fecha_r' => date('Y-m-d'),
    ];

    if (isset($_POST['returnedCheck'])) {
        $code = $_POST["code"];
        $codeInfo = DB::table('presupuesto')->where([
            ['codigo', $code],
            ["year", $year]
        ])->first();
        $data['codigo'] = $code;
        $data['pago'] = 0.00;
        $data['deuda'] = $amount * -1;
        $data['chkd'] = 5;
        $feeCode = $school->codc1;
        $feeAmount = $school->codc2;
        $feeCodeInfo = DB::table('presupuesto')->where([
            ['codigo', $feeCode],
            ["year", $year]
        ])->first();

        Payment::create([
            'id' => $student->id,
            'nombre' => "$student->nombre $student->apellidos",
            'desc1' => $feeCodeInfo->descripcion,
            'fecha_d' => $chargeDate,
            'year' => $year,
            'codigo' => $feeCodeInfo->codigo,
            'ss' => $student->ss,
            'grado' => $student->grado,
            'deuda' => $feeAmount,
        ]);

        Payment::create([
            'id' => $student->id,
            'nombre' => "$student->nombre $student->apellidos",
            'desc1' => $codeInfo->descripcion,
            'fecha_d' => $chargeDate,
            'year' => $year,
            'codigo' => $code,
            'ss' => $student->ss,
            'grado' => $student->grado,
            'deuda' => $amount,
        ]);
    }


    Payment::find($id)->update($data);


    Route::redirect("/billing/payments/index.php?accountId={$student->id}&month={$month}");
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $id = $_GET['id'];
    $charge = Payment::find($id);
    $student =  $charge->student;

    if ($charge) {
        $data = [
            "id" => intval($charge->mt),
            "chargeTo" => intval($student->mt),
            "bash" => $charge->bash,
            "amount" => floatval($charge->pago),
            "charge_date" => $charge->fecha_d,
            "payment_date" => $charge->fecha_p,
            "description" => $charge->desc1,
            "paymentType" => $charge->tdp,
            'user' => $charge->usuario,
            'time' => $charge->hora,
            'date' => $charge->fecha2,
            'checkNumber' => $charge->nuchk,
            'comment' => $charge->razon,
            'change_date' => $charge->fecha_r,
            'code' => $charge->codigo,
            'returnedCheck' => $charge->chkd


        ];
        echo json_encode($data);
    } else {
        echo json_encode(['error' => true]);
    }
} else {
    Route::error();
}
