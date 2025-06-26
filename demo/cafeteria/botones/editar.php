<?php

use App\Models\CafeteriaButton;
use Classes\DataBase\DB;

require_once '../../app.php';
$id = $_POST['id'];
$titulo = $_POST['titulo'];
$precio = $_POST['price'];
$precio2 = $_POST['price2'];
$precio_descuento = $_POST['discount_price'];
$image = $_POST['image'];

echo '<pre>';
var_dump($_POST);
echo '</pre>';
CafeteriaButton::find($id)->update([
    'articulo' => $titulo,
    'precio' => $precio,
    'precio2' => $precio2,
    'precio_descuento' => $precio_descuento,
    'foto' => $image
]);
header("Location: index.php");
