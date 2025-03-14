<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once '../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'] ?: null;
    $active = $_POST['active'] ? true : false;
    $prefix_code = $_POST['prefix_code'];
    $dateTime = date('Y-m-d H:i:s');

    if (empty($name) || empty($prefix_code)) {
        Route::redirect('/access/stores/create.php');
    }

    $storesWithPrefix = DB::table('stores')->where('prefix_code', $prefix_code)->first();

    if ($storesWithPrefix) {
        Route::redirect('/access/stores/create.php?error=prefix_code_exists');
    }

    DB::table('stores')->insert([
        'name' => $name,
        'description' => $description,
        'active' => $active,
        'prefix_code' => $prefix_code,
        'created_at' => $dateTime,
        'updated_at' => $dateTime,
    ]);

    Route::redirect('/access/stores/index.php');
}
Route::redirect('/access/stores/create.php');
