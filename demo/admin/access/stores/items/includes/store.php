<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once '../../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $storeId = $_POST['store_id'] ?? null;
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

    if (empty($name) || empty($storeId) || $price === null) {
        Route::redirect("/access/stores/items/create.php?store_id={$storeId}");
    }

    DB::table('store_items')->insert([
        'name' => $name,
        'store_id' => $storeId,
        'buy_multiple' => $buyMultiple,
        'price' => $price,
        'options' => $options,
        'picture_url' => $picture_url,
        'created_at' => $dateTime,
        'updated_at' => $dateTime,
    ]);
    Route::redirect("/access/stores/edit.php?id={$storeId}");
}
Route::redirect("/access/stores/");
