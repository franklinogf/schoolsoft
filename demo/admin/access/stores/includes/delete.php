<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;

    $dateTime = date('Y-m-d H:i:s');
    DB::table('stores')->where('id', $id)->delete();


    Route::redirect("/access/stores/");
}
Route::redirect('/access/stores/');
