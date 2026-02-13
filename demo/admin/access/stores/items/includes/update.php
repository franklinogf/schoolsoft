<?php

use App\Dtos\StoreItemOption;
use App\Models\StoreItem;
use App\Services\FileService;
use Classes\Route;
use Illuminate\Support\Carbon;

require_once __DIR__ . '/../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fileService = new FileService();
    $id = $_POST['id'] ?? null;
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

    $item = StoreItem::find($id);

    if (!$item) {
        Route::redirect("/access/stores/");
    }

    $storeId = $item->store_id;

    if (empty($name) || $price === null) {
        Route::redirect("/access/stores/items/edit.php?store_id={$storeId}&id={$id}");
    }

    $picture_url = $fileService->resolveStoreItemPictureUrl(
        $pictureSource,
        $pictureFile,
        $pictureUrlInput,
        $item->picture_url
    );

    $item->update([
        'name' => $name,
        'buy_multiple' => $buyMultiple,
        'options' => $options,
        'price' => $price,
        'picture_url' => $picture_url,
        'updated_at' => Carbon::now(),
    ]);

    Route::redirect("/access/stores/items/edit.php?store_id={$storeId}&id={$id}");
}
Route::redirect("/access/stores/");
