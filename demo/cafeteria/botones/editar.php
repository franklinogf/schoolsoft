<?php

use Classes\DataBase\DB;

require_once '../../app.php';
$id = $_POST['id'];
$titulo = $_POST['titulo'];
$precio = $_POST['precio'];
$image = $_POST['image'];

DB::table('T_cafeteria')->where('id', $id)->update([
    'articulo' => $titulo,
    'precio' => $precio,
    'foto' => $image
]);
header("Location: index.php");