<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;

require_once '../../app.php';
$colegio = new School();
$year = $colegio->year();
$metodo = ['1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'ID', '4' => 'Nombre'];

$fecha = date('Y-m-d');

if ($_POST['metodo'] === "4") {
	//metodo 4
	$cantidadDeposito = $_POST['cantidadDeposito'];
	$cantidadEfectivo = $_POST['cantidadEfectivo'];
	$cantidadPagar = $_POST['cantidadPagar'];

	$tdp2 = $_POST['tdp2'];

	$estu = DB::table('year')
		->select('id,ss,nombre,apellidos,grado,avisar')
		->where([['year', $year], ['ss', $_POST['estu']]])
		->orderBy('apellidos')
		->first();

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

	// $query = "INSERT INTO compra_cafeteria (`id2`,`nombre`, `apellido`, `ss`, `grado`, `fecha`, `tdp`, `total`, `year`,pago1,pago2,tdp2)
	//  VALUES ('$estu->id','$estu->nombre','$estu->apellidos','$estu->ss','$estu->grado','$fecha','{$_POST['metodo']}','{$TOTAL}','$year','$pago1','$pago2','$tdp2')";
	$id_compra = DB::table('compra_cafeteria')->insertGetId(
		[
			'id2' => $estu->id,
			'nombre' => $estu->nombre,
			'apellido' => $estu->apellidos,
			'ss' => $estu->ss,
			'grado' => $estu->grado,
			'fecha' => $fecha,
			'tdp' => $_POST['metodo'],
			'total' => $TOTAL,
			'year' => $year,
			'pago1' => $pago1,
			'pago2' => $pago2,
			'tdp2' => $tdp2
		]
	);

	DB::table('year')
	->where([['year', $year], ['ss', $_POST['estu']]])
	->update(['cantidad' => $descuento]);
	
	$estudiante = "$estu->nombre $estu->apellidos";
	$id_estudiante = $estu->id;
	$avisar = $estu->avisar;
} elseif ($_POST['metodo'] === "3") {
	// metodo 3
	$cantidadDeposito = $_POST['cantidadDeposito'];
	$tdp2 = $_POST['tdp2'];
	$cantidadEfectivo = $_POST['cantidadEfectivo'];

	$cantidadPagar = $_POST['cantidadPagar'];

	$estu = DB::table('year')
	->select('id,ss,nombre,apellidos,grado,avisar')
	->where([['year', $year], ['cbarra', $_POST['cbarra']]])
	->orderBy('apellidos')
	->first();
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

$id_compra = DB::table('compra_cafeteria')->insertGetId(
		[
			'id2' => $estu->id,
			'nombre' => $estu->nombre,
			'apellido' => $estu->apellidos,
			'ss' => $estu->ss,
			'grado' => $estu->grado,
			'fecha' => $fecha,
			'tdp' => $_POST['metodo'],
			'total' => $TOTAL,
			'year' => $year,
			'pago1' => $pago1,
			'pago2' => $pago2,
			'tdp2' => $tdp2
		]
	);
	DB::table('year')
	->where([['year', $year], ['cbarra', $_POST['cbarra']]])
	->update(['cantidad' => $descuento]);
	
	$estudiante = "$estu->nombre $estu->apellidos";
	$id_estudiante = $estu->id;
	$avisar = $estu->avisar;
} else {

	$id_compra = DB::table('compra_cafeteria')->insertGetId(
		[
			'fecha' => $fecha,
			'tdp' => $_POST['metodo'],
			'total' => $_POST['cantidadPagar'],
			'year' => $year
		]
	);
}


// Para aparecer en las ordenes
if ($_POST['metodo'] === '3' || $_POST['metodo'] === '4') {
DB::table('cafeteria_orders')->insert(
		[
			'ss' => $estu->ss,
			'id_compra' => $id_compra,
			'year' => $year
		]
	);
}


if (isset($_POST['credito']) && $_POST['credito'] === 'si') {

	DB::table('compra_cafeteria_detalle')->insert(
		[
			'id_compra' => $id_compra,
			'descripcion' => 'Un dolar por credito',
			'precio' => '1.00'
		]
	);
}

if (isset($_POST['id'])) {
	foreach ($_POST['id'] as $id) {
		
		$art = DB::table('T_cafeteria')->where('id', $id)->first();
		DB::table('compra_cafeteria_detalle')->insert(
			[
				'id_compra' => $id_compra,
				'descripcion' => $art->articulo,
				'precio' => $art->precio,
				'id_boton' => $id
			]
		);
	}
}


if (isset($_POST['barcode'])) {
	foreach ($_POST['barcode'] as $id) {

		// $res = mysql_query("SELECT * FROM inventario WHERE cbarra = '$id'");
		// $art = mysql_fetch_object($res);
		$art = DB::table('inventario')->where('cbarra', $id)->first();

		// mysql_query("INSERT INTO compra_cafeteria_detalle (id_compra,descripcion,precio,id_inv,cbarra) VALUES ('$id_compra','$art->articulo','$art->precio','$art->id2','$id')");
		DB::table('compra_cafeteria_detalle')->insert(
			[
				'id_compra' => $id_compra,
				'descripcion' => $art->articulo,
				'precio' => $art->precio,
				'id_inv' => $art->id2,
				'cbarra' => $id
			]
		);
		$cantidad = intval($art->cantidad) - 1;
		DB::table('inventario')->where('cbarra', $id)->update(['cantidad' => $cantidad]);
	}
}

// if ($_POST['metodo'] == 3 || $_POST['metodo'] == 4) {	
// 		$id_compra = (int)$id_compra;
// 		include 'recibo.php';	
// } else {
header("LOCATION: index.php");
// }
