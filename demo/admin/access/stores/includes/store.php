<?php

use App\Models\Store;
use Classes\Route;
use Illuminate\Support\Carbon;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'] ?: null;
    $active = $_POST['active'] ? true : false;
    $prefix_code = $_POST['prefix_code'];

    if (empty($name) || empty($prefix_code)) {
        Route::redirect('/access/stores/create.php');
    }

    $storesWithPrefix = Store::where('prefix_code', $prefix_code)->first();

    if ($storesWithPrefix) {
        Route::redirect('/access/stores/create.php?error=prefix_code_exists');
    }

    Store::create([
        'name' => $name,
        'description' => $description,
        'active' => $active,
        'prefix_code' => $prefix_code,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);

    Route::redirect('/access/stores/index.php');
}
Route::redirect('/access/stores/create.php');
