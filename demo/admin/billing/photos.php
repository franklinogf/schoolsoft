<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

if (isset($_GET['getAllPhotosTypes'])) {
    $result = DB::table('shopping_cart_photos_types')->get();
    $photos = [];
    foreach ($result as $row) {
        $photos[] = $row;
    }
    echo json_encode($photos);
}

if (isset($_POST['addNewPhotoType'])) {
    $result = DB::table('shopping_cart_photos_types')->insert([
        'name' => 'new type',
        'price' => 0,
        'description' => null,
    ]);
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

if (isset($_POST['updatePhotoType'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $result = DB::table('shopping_cart_photos_types')->where([
        ['id', $id]
    ])->update([
        'name' => $name,
        'price' => $price,
        'description' => $description,
    ]);
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

if (isset($_POST['deletePhotoType'])) {
    $id = $_POST['id'];
    $result = DB::table("shopping_cart_photos_types")->where([
        ['id', $id]
    ])->delete();
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
