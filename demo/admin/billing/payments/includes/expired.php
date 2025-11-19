<?php
use Classes\Controllers\Parents;
use Classes\Controllers\School;
use Classes\Route;
use Classes\DataBase\DB;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    $school = new School();
    $year = $school->year();

    $accountId = $_GET['accountId'];
    $month = $_GET['month'];

    $months = [];
    $monthNumber = intval($month);

    $lastYearMonths = $monthNumber <= 5;
    $lastMonth = $monthNumber <= 5 ? 1 : 8;
    for ($i = $monthNumber; $i >= $lastMonth; $i--) {
        array_push($months, $i < 10 ? "0$i" : strval($i));
    }
    if ($lastYearMonths) {
        for ($i = 1; $i < $lastMonth; $i++) {
            array_push($months, "0$i");
        }
        for ($i = 12; $i >= 8; $i--) {
            array_push($months, $i < 10 ? "0$i" : strval($i));
        }
    }
    //school year months
    $whereDates = "";
    foreach ($months as $index => $mon) {
        $whereDates .= $index > 0 ? " OR " : " AND (";
        $whereDates .= "MONTH(fecha_d) = '$mon'";
    }
    $whereDates .= ")";
    $charges = DB::table('pagos')->select("codigo,desc1,sum(deuda) as deuda,sum(pago) as pago")->whereRaw("id = ? AND year = ? $whereDates GROUP BY codigo", [$accountId, $year])->get();
    $data = [];
    foreach ($charges as $charge) {
        $total = floatval($charge->deuda) - floatval($charge->pago);
        $data[] = [
            'code' => $charge->codigo,
            'description' => $charge->desc1,
            'debt' => $total,
        ];
    }

    echo json_encode($data);

} else {
    Route::error();

}
