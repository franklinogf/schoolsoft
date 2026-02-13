<?php

use App\Dtos\StoreItemOption;
use App\Models\StoreItem;
use App\Services\FileService;
use Classes\Route;
use Illuminate\Support\Carbon;

require_once __DIR__ . '/../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fileService = new FileService();
    $storeId = $_POST['store_id'] ?? null;
    $name = $_POST['name'];
    $buyMultiple = !empty($_POST['buy_multiple']);
    $price = $_POST['price'] ?: null;
    $pictureSource = $_POST['picture_source'] ?? 'url';
    $pictureFile = $_FILES['picture_upload'] ?? null;
    $pictureUrlInput = $_POST['picture_url'] ?? null;
    $options = [];
    foreach ($_POST['options'] ?? [] as $index => $option) {
        $options[] = new StoreItemOption(
            $option['name'],
            $option['price'] ?: null,
            $index
        );
    }

    if (empty($name) || empty($storeId) || $price === null) {
        Route::redirect("/access/stores/items/create.php?store_id={$storeId}");
    }

    $picture_url = $fileService->resolveStoreItemPictureUrl(
        $pictureSource,
        $pictureFile,
        $pictureUrlInput,
        null
    );

    StoreItem::create([
        'name' => $name,
        'store_id' => $storeId,
        'buy_multiple' => $buyMultiple,
        'price' => $price,
        'options' => $options,
        'picture_url' => $picture_url,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);

    Route::redirect("/access/stores/edit.php?id={$storeId}");
}
Route::redirect("/access/stores/");
