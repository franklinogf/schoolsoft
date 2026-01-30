<?php

use App\Models\Store;
use Classes\Route;
use Illuminate\Support\Carbon;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'] ?: null;
    $active = $_POST['active'] ? true : false;
    $prefix_code = $_POST['prefix_code'];

    if (empty($name) || empty($prefix_code)) {
        Route::redirect("/access/stores/edit.php?id={$id}");
    }

    $storesWithPrefix = Store::where('prefix_code', $prefix_code)
        ->where('id', '!=', $id)
        ->first();

    if ($storesWithPrefix) {
        Route::redirect("/access/stores/edit.php?id={$id}&error=prefix_code_exists");
    }

    Store::where('id', $id)->update([
        'name' => $name,
        'description' => $description,
        'active' => $active,
        'prefix_code' => $prefix_code,
        'updated_at' => Carbon::now(),
    ]);


    Route::redirect("/access/stores/edit.php?id={$id}");
}
Route::redirect('/access/stores/');
