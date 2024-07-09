<?php
use Classes\Route;
use Classes\DataBase\DB;

require_once '../../../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $id = $_POST['id'];

    try {
        DB::table('pagos')->where("mt", $id)->delete();
    } catch (Exception $e) {
        throw $e;
    }

    echo json_encode(['success' => true]);



} else {
    Route::error();
}
