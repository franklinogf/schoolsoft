<?php

use App\Models\Family;
use Classes\Route;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $accountId = $_POST['accountId'];
    Manager::table('madre')->where('id', $accountId)->update([
        "salerta" => $_POST['observationType'],
        "alerta" => $_POST['alert'] === 'on' ? 'Si' : '',
        "obs" => $_POST['info'],
        "fechk" => date('Y-m-d')
    ]);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $accountId = $_GET['accountId'];

    $family = Family::find($accountId);
    $data = [
        "observationType" => $family->salerta,
        "alert" => $family->alerta === 'Si' ? true : false,
        "info" => $family->obs,
    ];
    echo json_encode($data);
} else {
    Route::error();
}
