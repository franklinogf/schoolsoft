<?php
use Classes\Controllers\Parents;
use Classes\Route;
use Classes\DataBase\DB;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $accountId = $_POST['accountId'];
    if (isset($_POST['deletePromise'])) {
        DB::table('madre')->where('id', $accountId)->update([
            'fecha_p' => '',
            'fecha_e' => '',
            'promesa' => '',
            'cantidad_acordada' => '',
            'tiempo_acordado' => '',
            'nuevos_cargos' => '',
            'total_pagar' => '',
        ]);
    } else {
        DB::table('madre')->where('id', $accountId)->update([
            'fecha_p' => $_POST['date'],
            'fecha_e' => $_POST['expirationDate'],
            'promesa' => $_POST['description'],
            'cantidad_acordada' => $_POST['amount'],
            'tiempo_acordado' => $_POST['time'],
            'nuevos_cargos' => $_POST['newAmount'],
            'total_pagar' => $_POST['total'],
        ]);
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $accountId = $_GET['accountId'];

    $parent = new Parents($accountId);
    $data = [
        'date' => $parent->fecha_p,
        'expirationDate' => $parent->fecha_e,
        'description' => $parent->promesa,
        'amount' => $parent->cantidad_acordada ? floatval($parent->cantidad_acordada) : null,
        'time' => $parent->tiempo_acordado,
        'newAmount' => $parent->nuevos_cargos ? floatval($parent->nuevos_cargos) : null,
        'total' => $parent->total_pagar ? floatval($parent->total_pagar) : null,
    ];
    echo json_encode($data);

} else {
    Route::error();

}
