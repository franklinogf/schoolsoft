<?php

use App\Models\StoreItem;
use Classes\Route;

require_once __DIR__ . '/../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $item = StoreItem::find($id);

    if ($item) {
        $storeId = $item->store_id;
        $item->delete();
        Route::redirect("/access/stores/edit.php?id={$storeId}");
    }
}
Route::redirect('/access/stores/');
