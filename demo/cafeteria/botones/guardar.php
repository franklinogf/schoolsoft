<?php

use App\Models\CafeteriaButton;
use Classes\DataBase\DB;

require_once __DIR__ . '/../../app.php';

$titulo = $_POST['titulo'];
$precio = $_POST['price'];
$precio2 = $_POST['price2'];
$precio_descuento = $_POST['discount_price'];
$image = $_POST['image'];
$orden = $_POST['orden'];


CafeteriaButton::create([
    'articulo' => $titulo,
    'precio' => $precio,
    'precio2' => $precio2,
    'precio_descuento' => $precio_descuento,
    'foto' => $image,
    'orden' => $orden
]);
header("Location: index.php");
