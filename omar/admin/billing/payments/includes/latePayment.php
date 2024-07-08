<?php
use Classes\Controllers\Parents;
use Classes\Route;
use Classes\DataBase\DB;

require_once '../../../../app.php';


if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $accountId = $_POST['accountId'];
    DB::table('madre')->where('id', $accountId)->update([
        "salerta" => $_POST['observationType'],
        "alerta" => $_POST['alert'] === 'on' ? 'Si' : '',
        "obs" => $_POST['info'],
        "fechk" => date('Y-m-d')
    ]);

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $accountId = $_GET['accountId'];

    $parent = new Parents($accountId);
    $data = [
        "observationType" => $parent->salerta,
        "alert" => $parent->alerta === 'Si' ? true : false,
        "info" => $parent->obs,
    ];
    echo json_encode($data);

} else {
    Route::error();

}
