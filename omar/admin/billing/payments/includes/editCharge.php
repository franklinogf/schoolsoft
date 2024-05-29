<?php
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\School;

require_once '../.././../../app.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === 'PUT') {
    echo '<pre>';
    var_dump($_POST);
    echo '</pre>';
    exit;


    // Route::redirect("/billing/payments?accountId={$student->id}&month={$month}");


} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = $_GET['id'];
    $charge = DB::table('pagos')->where('mt', -100)->first();
    if ($charge) {
        echo json_encode($charge);
    } else {
        echo json_encode(['error' => true]);
    }

} else {
    Route::error();

}
