<?php

use App\Models\Admin;
use App\Models\Student;
use Classes\Route;
use Illuminate\Database\Capsule\Manager;

require_once '../../../../app.php';
$depositTypes = [
    1 => "Cash",
    2 => "Donación",
    3 => "Intercambio LT",
    4 => "Recompensa",
    5 => "Correción",
    6 => "Devolución Balance",
    10 => 'Correción ACH',
    11 => 'Correción Tarjeta',
    7 => "Borrar",
    8 => "Balance",
    9 => "Otros",
    12 => "Pago a través de oficina",
];

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $id = $_POST['id'];
    $school = Admin::primaryAdmin();
    $year = $school->year();

    $date = $_POST['date'] ?? date('Y-m-d');
    $time = $_POST['time'] ?? date('H:i:s');

    $student = Student::find($id);

    if (isset($_POST['minDeposit'])) {
        $result = $student->update([
            "cantidad_alerta" => $_POST['minDeposit']
        ]);
        echo json_encode(["error" => $result]);
    } else if (isset($_POST['deleteDeposit'])) {
        $student->update([
            'cantidad' => 0.00,
            'f_deposito' => $date,
        ]);
        echo json_encode(["error" => false]);
    } else {

        $type = intval($_POST['type']);
        $type2 = $_POST['type2'];
        $amount = floatval($_POST['amount']);
        $other = $_POST['other'];
        $oldAmount = floatval($student->cantidad);
        if ($type2 === '0') {
            $newAmount = $oldAmount + $amount;
        } else {
            $newAmount = $oldAmount;
        }

        $selectedType = $depositTypes[$type];
        if ($type === 1 || $type === 2 || $type === 3 || $type === 4 || $type === 9 || $type === 12) {
            $date = date('Y-m-d');
            $time = date('H:i:s');
        }

        $data = [
            'id' => $student->id,
            'ss' => $student->ss,
            'fecha' => $date,
            'year' => $year,
            'cantidad' => $amount,
            'grado' => $student->grado,
            'tipoDePago' => utf8_encode($selectedType),
            'hora' => $time,
            'email' => $type2,
            'date' => $date . ' ' . $time,
        ];
        if ($type === 1 || $type === 2 || $type === 4 || $type === 5 || $type === 9 || $type === 10 || $type === 11 || $type === 12) {
            if ($type === 9) {
                $data['otros'] = $_POST['other'];
            }
            if ($type2 === '0') {
                Manager::table('depositos')->insert($data);
                $student->update([
                    'cantidad' => $newAmount,
                    'f_deposito' => $date,
                ]);
            } else {
                $data2 = [
                    'accountID' => $student->id,
                    'deliveryTo' => $student->ss,
                    'date' => $date . ' ' . $time,
                    'year' => $year,
                    'total' => $amount,
                    'payment_type' => utf8_encode($selectedType),
                    'shopping' => $type2,
                ];
                Manager::table('compras')->insert($data2);

                $nrec = Manager::table('compras')->orderBy('id', 'desc')->limit(1)->first();

                $data2 = [
                    'id_compra' => $nrec->id ?? '',
                    'year' => $year,
                    'price' => $amount,
                    'item_name' => $student->ss,
                ];
                Manager::table('compras_detalle')->insert($data2);
            }
        } else if ($type === 6) {
            $data['cantidad'] = floatval($student->cantidad) * -1;
            Manager::table('depositos')->insert($data);
            $student->update([
                'cantidad' => 0.00,
                'f_deposito' => $date,
            ]);
        }
    }

    // $date = $_POST['date'];
    // $chargeTo = $_POST['chargeTo'];
    // $description = $_POST['description'];
    // $amount = $_POST['amount'];
    // $chargeId = $_POST['chargeId'];

    // $student = new Student($chargeTo);

    // $month = date('m', strtotime($date));


    // DB::table('pagos')->where('mt', $id)->update([
    //     'nombre' => "$student->nombre $student->apellidos",
    //     'desc1' => $description,
    //     'fecha_d' => $date,
    //     'ss' => $student->ss,
    //     'grado' => $student->grado,
    //     'deuda' => $amount,
    // ]);


    // Route::redirect("/billing/payments?accountId={$student->id}&month={$month}");


} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $id = $_GET['id'];
    $student = Student::find($id);

    if ($student) {
        $data = [
            "id" => intval($student->mt),
            "minDeposit" => floatval($student->cantidad_alerta),
            "deposit" => floatval($student->cantidad),
        ];
        echo json_encode($data);
    } else {
        echo json_encode(['error' => true]);
    }
} else {
    Route::error();
}
