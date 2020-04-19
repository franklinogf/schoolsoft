<?php 
require_once '../../../app.php';

$ID = $_SESSION['id'];
$entrada=$_POST['entrada'];
$desc = $_POST['desc'];
$fecha = date("Y-m-d");
$hora = date("H:i:s");
$result = mysql_query("SELECT year FROM colegio WHERE usuario='administrador'",$con);
$row = mysql_fetch_assoc($result);
$year = $row['year'];
mysql_query("INSERT INTO detalle_foro_entradas (creador_id,tipo,entrada_id,descripcion,fecha,hora,year)
	VALUES ('$ID','p','$entrada','$desc','$fecha','$hora','$year')",$con);
header("Location: verentrada.php?entrada=$entrada");
?>