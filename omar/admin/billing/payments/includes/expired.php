<?php
use Classes\Controllers\Parents;
use Classes\Route;
use Classes\DataBase\DB;

require_once '../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $accountId = $_GET['accountId'];
    $charges = DB::table('pagos')->select("codigo,desc1,sum(deuda) as deuda,sum(pago) as pago")->whereRaw("id = ? GROUP BY codigo", [$accountId])->get();
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
