<?php

use App\Models\Admin;
use App\Models\Student;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../../app.php';

date_default_timezone_set("America/Puerto_Rico");
$school = Admin::primaryAdmin();

$year = $school->year;

$date = date('Y-m-d');
$time = date('H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = json_decode(file_get_contents('php://input'));

    $totalToPay = (float) $inputs->total;
    $studentID = $inputs->studentID;
    $itemsToOrder = $inputs->items;
    if (!$totalToPay || !$studentID || count($itemsToOrder) < 1) {
        http_response_code(400);
        return;
    }
    // Look for student info
    $student = Student::where('mt', $studentID)->first();
    $depositAmount = (float) $student->cantidad;

    // Check if charge 1 credit is needed
    $hasCreditCharge = $totalToPay > $depositAmount;
    // If the it has to be 1 credit charge add 1 dollar to the total amount
    $total = $hasCreditCharge ? $totalToPay + 1 : $totalToPay;

    $newDepositAmount = (float) ($depositAmount - $total);

    // Update the deposit amount of the student

    $student->update(['cantidad' => $newDepositAmount]);

    // Insert the order to the cafeteria table   
    $orderID = Manager::table('compra_cafeteria')->insertGetId([
        'id2' => $student->id,
        'nombre' => $student->nombre,
        'apellido' => $student->apellidos,
        'ss' => $student->ss,
        'grado' => $student->grado,
        'fecha' => $date,
        'tdp' => '5',
        'total' => $total,
        'year' => $year,
        'pago1' => $total,
        'pago2' => '0.00',
        'tdp2' => '5',
        'cn' => '2',
        'hora' => $time,
    ]);


    // Apply 1 dollar credit if the total to pay is greater than the deposit amount
    if ($hasCreditCharge) {
        Manager::table('compra_cafeteria_detalle')->insert([
            'id_compra' => $orderID,
            'descripcion' => 'Un dolar por credito',
            'precio' => '1.00',
            'ss' => $student->ss,
            'year' => $year,
            'cn' => '2'
        ]);
    }

    foreach ($itemsToOrder as $item) {
        Manager::table('compra_cafeteria_detalle')->insert([
            'id_compra' => $orderID,
            'descripcion' => $item->label,
            'precio' => $item->price,
            'id_boton' => $item->id,
            'ss' => $student->ss,
            'year' => $year,
            'cn' => '2'
        ]);
    }



    // Insert the order to the order history
    Manager::table('cafeteria_orders')->insert([
        'ss' => $student->ss,
        'id_compra' => $orderID,
        'year' => $year
    ]);
}
