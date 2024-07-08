<?php
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\School;

require_once '../../../../app.php';
$depositTypes = [
    1 => "Cash",
    2 => "Donación",
    3 => "Intercambio LT",
    4 => "Recompensa",
    5 => "Correción",
    6 => "Devolución Balance",
    7 => "Borrar",
    8 => "Balance",
    9 => "Otros",
];

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $id = $_POST['id'];
    $school = new School();
    $year = $school->year();
    $date = date('Y-m-d');

    if (isset($_POST['minDeposit'])) {
        $result = DB::table('year')->where('mt', $id)->update([
            "cantidad_alerta" => $_POST['minDeposit']
        ]);
        echo json_encode(["error" => $result]);
    } else if (isset($_POST['deleteDeposit'])) {
        DB::table('year')->where('mt', $id)->update([
            'cantidad' => 0.00,
            'f_deposito' => $date,
        ]);
        echo json_encode(["error" => false]);
    } else {
        $student = new Student($id);
        $type = intval($_POST['type']);
        $amount = floatval($_POST['amount']);
        $other = $_POST['other'];
        $oldAmount = floatval($student->cantidad);
        $newAmount = $oldAmount + $amount;

        $time = date('H:m:i');
        $selectedType = $depositTypes[$type];
        $data = [
            'id' => $student->id,
            'ss' => $student->ss,
            'fecha' => $date,
            'year' => $year,
            'cantidad' => $amount,
            'grado' => $student->grado,
            'tipoDePago' => $selectedType,
            'hora' => $time,
        ];
        if ($type === 1 || $type === 2 || $type === 4 || $type === 5 || $type === 9) {
            if ($type === 9) {
                $data['otros'] = $_POST['other'];
            }
            DB::table('depositos')->insert($data);
            DB::table('year')->where('mt', $id)->update([
                'cantidad' => $newAmount,
                'f_deposito' => $date,
            ]);
        } else if ($type === 6) {
            $data['cantidad'] = floatval($student->cantidad) * -1;
            DB::table('depositos')->insert($data);
            DB::table('year')->where('mt', $id)->update([
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
    $student = new Student($id);

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
