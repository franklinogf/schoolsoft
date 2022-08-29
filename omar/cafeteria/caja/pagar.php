<?php
include('../../control.php');
$ra = mysql_query("SELECT * FROM colegio where usuario = 'administrador'");
$colegio = mysql_fetch_object($ra);
$metodo = ['1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'ID', '4' => 'Nombre'];

// echo "<pre>";
// var_dump($_POST);
// echo "<hr/>";
// exit;
$fecha = date('Y-m-d');

if ($_POST['metodo'] === "4") {
	//metodo 4
	$cantidadDeposito = $_POST['cantidadDeposito'];
	$cantidadEfectivo = $_POST['cantidadEfectivo'];
	$cantidadPagar = $_POST['cantidadPagar'];

	$tdp2 = $_POST['tdp2'];

	$estudiantes = mysql_query("SELECT id,ss,nombre,apellidos,grado,avisar FROM year where year = '$colegio->year' and ss= '{$_POST['estu']}' ORDER BY apellidos");
	$estu = mysql_fetch_object($estudiantes);

	if ($cantidadPagar > $cantidadDeposito) {
		$pago1 = $cantidadDeposito;
		$pago2 = $cantidadPagar - $cantidadDeposito;
	} else {
		$pago1 = $cantidadPagar;
		$pago2 = 0;
	}
	$descuento = $cantidadDeposito - $cantidadPagar;
	$descuento = ($descuento < 0) ? 0 : $descuento;

	if (isset($_POST['credito']) && $_POST['credito'] === 'si') {
		$descuento = $cantidadDeposito < 0 ? $cantidadDeposito - $cantidadEfectivo : $cantidadEfectivo * -1;
		$tdp2 = 'Deuda';
		$pago1 = $cantidadDeposito < 0 ? 0 : $cantidadDeposito;
		$pago2 = $cantidadEfectivo;
	}
	$TOTAL = $pago1 + $pago2;

	$query = "INSERT INTO compra_cafeteria (`id2`,`nombre`, `apellido`, `ss`, `grado`, `fecha`, `tdp`, `total`, `year`,pago1,pago2,tdp2) VALUES ('$estu->id','$estu->nombre','$estu->apellidos','$estu->ss','$estu->grado','$fecha','{$_POST['metodo']}','{$TOTAL}','$colegio->year','$pago1','$pago2','$tdp2')";

	mysql_query("UPDATE year SET cantidad = '$descuento' WHERE year = '$colegio->year' and ss= '{$_POST['estu']}'");
	$estudiante = "$estu->nombre $estu->apellidos";
	$id_estudiante = $estu->id;
	$avisar = $estu->avisar;
} elseif ($_POST['metodo'] === "3") {
	// metodo 3
	$cantidadDeposito = $_POST['cantidadDeposito'];
	$tdp2 = $_POST['tdp2'];
	$cantidadEfectivo = $_POST['cantidadEfectivo'];

	$cantidadPagar = $_POST['cantidadPagar'];


	$estudiantes = mysql_query("SELECT id,ss,nombre,apellidos,grado,avisar FROM year where year = '$colegio->year' and cbarra= '{$_POST['cbarra']}' ORDER BY apellidos");
	$estu = mysql_fetch_object($estudiantes);

	if ($cantidadPagar > $cantidadDeposito) {
		$pago1 = $cantidadDeposito;
		$pago2 = $cantidadPagar - $cantidadDeposito;
	} else {
		$pago1 = $cantidadPagar;
		$pago2 = 0;
	}

	$tdp2 = ($pago2 > 0) ? $tdp2 : null;
	$descuento = $cantidadDeposito - $cantidadPagar;
	$descuento = ($descuento < 0) ? 0 : $descuento;
	if (isset($_POST['credito'])  && $_POST['credito'] === 'si') {
		$descuento = $cantidadDeposito < 0 ? $cantidadDeposito - $cantidadEfectivo : $cantidadEfectivo * -1;
		$tdp2 = 'Deuda';
		$pago1 = $cantidadDeposito < 0 ? 0 : $cantidadDeposito;
		$pago2 = $cantidadEfectivo;
	}
	$TOTAL = $pago1 + $pago2;
	$query = "INSERT INTO compra_cafeteria (`id2`,`nombre`, `apellido`, `ss`, `grado`, `fecha`, `tdp`, `total`, `year`,pago1,pago2,tdp2) VALUES ('$estu->id','$estu->nombre','$estu->apellidos','$estu->ss','$estu->grado','$fecha','{$_POST['metodo']}','{$TOTAL}','$colegio->year','$pago1','$pago2','$tdp2')";


	mysql_query("UPDATE year SET cantidad = '$descuento' WHERE year = '$colegio->year' and cbarra= '{$_POST['cbarra']}'");
	$estudiante = "$estu->nombre $estu->apellidos";
	$id_estudiante = $estu->id;
	$avisar = $estu->avisar;
} else {

	$query = "INSERT INTO compra_cafeteria (`fecha`, `tdp`, `total`, `year`) VALUES ('$fecha','{$_POST['metodo']}','{$_POST['cantidadPagar']}','$colegio->year')";
}


mysql_query($query);
$id_compra = mysql_insert_id();

// Para aparecer en las ordenes
if ($_POST['metodo'] === '3' || $_POST['metodo'] === '4') {
	mysql_query("INSERT INTO cafeteria_orders (ss,id_compra,year) VALUES ('$estu->ss','$id_compra','$colegio->year')");
}


if (isset($_POST['credito']) && $_POST['credito'] === 'si') {
	mysql_query("INSERT INTO compra_cafeteria_detalle (id_compra,descripcion,precio) VALUES ('$id_compra','Un dolar por credito','1.00')");
}

if (isset($_POST['id'])) {
	foreach ($_POST['id'] as $id) {

		$res = mysql_query("SELECT * FROM T_cafeteria WHERE id = '$id'");
		$art = mysql_fetch_object($res);
		mysql_query("INSERT INTO compra_cafeteria_detalle (id_compra,descripcion,precio,id_boton) VALUES ('$id_compra','$art->articulo','$art->precio','$id')");
	}
}


if (isset($_POST['barcode'])) {
	foreach ($_POST['barcode'] as $id) {

		$res = mysql_query("SELECT * FROM inventario WHERE cbarra = '$id'");
		$art = mysql_fetch_object($res);

		mysql_query("INSERT INTO compra_cafeteria_detalle (id_compra,descripcion,precio,id_inv,cbarra) VALUES ('$id_compra','$art->articulo','$art->precio','$art->id2','$id')");
		$cantidad = $art->cantidad - 1;
		mysql_query("UPDATE inventario SET cantidad='$cantidad' WHERE cbarra = '$id'");
	}
}

// if ($_POST['metodo'] == 3 || $_POST['metodo'] == 4) {	
// 		$id_compra = (int)$id_compra;
// 		include 'recibo.php';	
// } else {
header("LOCATION: index.php");
// }
