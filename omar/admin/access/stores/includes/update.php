<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once '../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'] ?: null;
    $active = $_POST['active'] ? true : false;
    $prefix_code = $_POST['prefix_code'];

    $dateTime = date('Y-m-d H:i:s');

    if (empty($name) || empty($prefix_code)) {
        Route::redirect("/access/stores/edit.php?id={$id}");
    }

    $storesWithPrefix = DB::table('stores')->where([
        ['prefix_code', $prefix_code],
        ['id', '!=', $id]
    ])->first();

    if ($storesWithPrefix) {
        Route::redirect("/access/stores/edit.php?id={$id}&error=prefix_code_exists");
    }

    DB::table('stores')->where('id', $id)->update([
        'name' => $name,
        'description' => $description,
        'active' => $active,
        'prefix_code' => $prefix_code,
        'updated_at' => $dateTime,
    ]);


    Route::redirect("/access/stores/edit.php?id={$id}");
}
Route::redirect('/access/stores/');
