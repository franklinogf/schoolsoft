<?php

use Classes\DataBase\DB;

require_once '../../app.php';

$titulo = $_POST['titulo'];
$precio = $_POST['precio'];
$image = $_POST['image'];
$orden = $_POST['orden'];


DB::table('T_cafeteria')->insert([
    'articulo' => $titulo,
    'precio' => $precio,
    'foto' => $image,
    'orden' => $orden
]);
header("Location: index.php");