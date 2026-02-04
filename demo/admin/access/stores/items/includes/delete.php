<?php

use App\Models\StoreItem;
use App\Services\FileService;
use Classes\Route;

require_once __DIR__ . '/../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fileService = new FileService();
    $id = $_POST['id'] ?? null;
    $item = StoreItem::find($id);

    if ($item) {
        $fileService->deleteStoreItemPicture($item->picture_url);
        $storeId = $item->store_id;
        $item->delete();
        Route::redirect("/access/stores/edit.php?id={$storeId}");
    }
}
Route::redirect('/access/stores/');
