<?php

use App\Dtos\StoreItemOption;
use App\Models\StoreItem;
use Classes\Route;
use Illuminate\Support\Carbon;

require_once __DIR__ . '/../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $buyMultiple = $_POST['buy_multiple'] ? true : false;
    $price = $_POST['price'] ?: null;
    $picture_url = $_POST['picture_url'] ?: null;
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
