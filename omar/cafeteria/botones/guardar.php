<?php 
include('../../control.php'); 

$titulo = $_POST['titulo'];
$precio = $_POST['precio'];
$image = $_POST['image'];
$orden = $_POST['orden'];

mysql_query("INSERT INTO T_cafeteria (articulo,precio,foto,orden) VALUES ('$titulo','$precio','$image','$orden')");

header("Location: index.php");