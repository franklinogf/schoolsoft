<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once __DIR__ . '/../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $item = DB::table('store_items')->where('id', $id)->first();

    $dateTime = date('Y-m-d H:i:s');
    DB::table('store_items')->where('id', $id)->delete();


    Route::redirect("/access/stores/edit.php?store_id={$item->store_id}");
}
Route::redirect('/access/stores/');
