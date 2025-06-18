<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once '../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $buyMultiple = $_POST['buy_multiple'] ? true : false;
    $price = $_POST['price'] ?: null;
    $picture_url = $_POST['picture_url'] ?: null;
    $dateTime = date('Y-m-d H:i:s');
    $options = [];
    foreach ($_POST['options'] ?? [] as $index => $option) {
        $options[] = [
            'name' => $option['name'],
            'price' => $option['price'] ?: null,
            'order' => $index,
        ];
    }
    $options = count($options) > 0 ? json_encode($options) : null;
    $item = DB::table('store_items')->where('id', $id)->first();
    $storeId = $item->store_id;
    if (empty($name) || $price === null) {
        Route::redirect("/access/stores/items/edit.php?store_id={$storeId}&id={$id}");
    }

    DB::table('store_items')->where('id', $id)->update([
        'name' => $name,
        'buy_multiple' => $buyMultiple,
        'options' => $options,
        'price' => $price,
        'picture_url' => $picture_url,
        'updated_at' => $dateTime,
    ]);

    Route::redirect("/access/stores/items/edit.php?store_id={$storeId}&id={$id}");
}
Route::redirect("/access/stores/");
