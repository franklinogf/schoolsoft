<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once '../../app.php';
if (isset($_POST['add'])) {
    $itemId = DB::table('inventario')->insert([
        'id2' => $_POST['id2'],
        'articulo' => $_POST['articulo'],
        'precio' => $_POST['precio'],
        'cbarra' => $_POST['cbarra'],
        'cantidad' => $_POST['cantidad'],
        'minimo' => $_POST['minimo'],
    ], true);
    Route::redirect("/inventario.php?item=$itemId");
} else if (isset($_POST['edit'])) {
    $itemId = $_POST['id'];
    DB::table('inventario')
        ->where('id', $itemId)
        ->update([
            'minimo' => $_POST['minimo'],
            'cbarra' => $_POST['cbarra'],
            'id2' => $_POST['id2'],
            'articulo' => $_POST['articulo'],
            'precio' => $_POST['precio'],
            'cantidad' => $_POST['cantidad'],
        ]);
    Route::redirect("/inventario.php?item=$itemId");
} else if (isset($_POST['delete'])) {
    DB::table('inventario')->where('id', $_POST['id'])->delete();
    Route::redirect('/inventario.php');
}
