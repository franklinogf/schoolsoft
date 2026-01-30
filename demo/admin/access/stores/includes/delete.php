<?php

use App\Models\Store;
use Classes\Route;

require_once __DIR__ . '/../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;

    Store::destroy($id);

    Route::redirect("/access/stores/");
}
Route::redirect('/access/stores/');
