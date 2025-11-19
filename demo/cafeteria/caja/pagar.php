<?php

use App\Models\Admin;
use App\Models\CafeteriaOrder;
use App\Models\Student;
use Classes\DataBase\DB;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../app.php';
$admin = Admin::primaryAdmin();
$year = $admin->year;

$metodo = ['1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'ID', '4' => 'Nombre'];

$fecha = date('Y-m-d');

if ($_POST['metodo'] === "4") {
	//metodo 4
	$cantidadDeposito = $_POST['cantidadDeposito'];
	$cantidadEfectivo = $_POST['cantidadEfectivo'];
	$cantidadPagar = $_POST['cantidadPagar'];

	$tdp2 = $_POST['tdp2'];

	$estu = Student::bySS($_POST['estu'])->first();

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

	$order = CafeteriaOrder::create([
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
	]);

	$estu->update(['cantidad' => $descuento]);

	$estudiante = "$estu->nombre $estu->apellidos";
	$id_estudiante = $estu->id;
	$avisar = $estu->avisar;
} elseif ($_POST['metodo'] === "3") {
	// metodo 3
	$cantidadDeposito = $_POST['cantidadDeposito'];
	$tdp2 = $_POST['tdp2'];
	$cantidadEfectivo = $_POST['cantidadEfectivo'];

	$cantidadPagar = $_POST['cantidadPagar'];

	$estu = Student::bySS($_POST['estu'])->first();
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

	$order = CafeteriaOrder::create([
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
	]);

	$estu->update(['cantidad' => $descuento]);

	$estudiante = "$estu->nombre $estu->apellidos";
	$id_estudiante = $estu->id;
	$avisar = $estu->avisar;
} else {

	$order = CafeteriaOrder::create([
		'fecha' => $fecha,
		'tdp' => $_POST['metodo'],
		'total' => $_POST['cantidadPagar'],
		'year' => $year
	]);
}


// Para aparecer en las ordenes
if ($_POST['metodo'] === '3' || $_POST['metodo'] === '4') {
	Manager::table('cafeteria_orders')->insert(
		[
			'ss' => $estu->ss,
			'id_compra' => $order->id,
			'year' => $year
		]
	);
}


if (isset($_POST['credito']) && $_POST['credito'] === 'si') {

	$order->items()->create(
		[
			'descripcion' => 'Un dolar por credito',
			'precio' => '1.00'
		]
	);
}

if (isset($_POST['id'])) {
	foreach ($_POST['id'] as $id) {

		$art = DB::table('T_cafeteria')->where('id', $id)->first();
		$order->items()->create(
			[
				'descripcion' => $art->articulo,
				'precio' => $art->precio,
				'id_boton' => $id
			]
		);
	}
}


if (isset($_POST['barcode'])) {
	foreach ($_POST['barcode'] as $id) {

		$art = DB::table('inventario')->where('cbarra', $id)->first();

		$order->items()->create(
			[
				'descripcion' => $art->articulo,
				'precio' => $art->precio,
				'id_inv' => $art->id2,
				'cbarra' => $id
			]
		);

		$cantidad = intval($art->cantidad) - 1;
		Manager::table('inventario')->where('cbarra', $id)->update(['cantidad' => $cantidad]);
	}
}


header("LOCATION: index.php");
exit;
