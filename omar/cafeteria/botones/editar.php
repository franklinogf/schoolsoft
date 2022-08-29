<?php 
include('../../control.php'); 
$id = $_POST['id'];
$titulo = $_POST['titulo'];
$precio = $_POST['precio'];
$image = $_POST['image'];

mysql_query("UPDATE T_cafeteria SET articulo = '$titulo',precio = '$precio',foto = '$image' WHERE id = '$id'");

header("Location: index.php");