<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\PDF;

require_once '../app.php';

$school = new School();
$year = $school->year();

$metodo = ['1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'ID', '4' => 'Nombre', '5' => 'ATH'];


$purchases = DB::table('compra_cafeteria')->where([
	['year', $year],
	['fecha', '>=', $_POST['fecha1']],
	['fecha', '<=', $_POST['fecha2']]
])->orderBy('fecha')->get();

$pdf = new PDF();
$pdf->Fill();
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage();
$pdf->SetFont('Times', 'B', 12);

$pdf->Cell(0, 5, "Desde {$_POST['fecha1']} Hasta {$_POST['fecha2']}", 0, 1);
$pdf->Ln(3);

$pdf->Cell(20, 5, '#', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Total Vendido', 1, 1, 'C', true);




$pdf->SetFont('Times', '', 12);
$precio = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
$articulos = [
	'inventario' => [],
	'boton' => []
];
$TOTAL = 0;
foreach ($purchases as $purchase) {
	$pucharseItems = DB::table('compra_cafeteria_detalle')->where([
		['id_compra', $purchase->id],
		['id_inv', '<>', '']
	])->get();
	foreach ($purchaseItems as $purchaseItem) {
		$id = $purchaseItem->id_inv;
		$price = $purchaseItem->precio_final ? (float) $purchaseItem->precio_final : (float) $purchaseItem->precio;
		$articulos['inventario'][$id]['nombre'] = $purchaseItem->descripcion;
		$articulos['inventario'][$id]['precio'] += $price;
		++$articulos['inventario'][$id]['cantidad'];


		$precio[$purchase->tdp] += $price;
		$TOTAL += number_format($price, 2);
	}

	$pucharseItems = DB::table('compra_cafeteria_detalle')->where([
		['id_compra', $purchase->id],
		['id_boton', '<>', '']
	])->get();
	foreach ($purchaseItems as $purchaseItem) {
		$price = $purchaseItem->precio_final ? (float) $purchaseItem->precio_final : (float) $purchaseItem->precio;
		$id = $purchaseItem->id_boton;
		$articulos['boton'][$id]['nombre'] = $purchaseItem->descripcion;
		$articulos['boton'][$id]['precio'] += $price;
		++$articulos['boton'][$id]['cantidad'];


		$precio[$purchase->tdp] += $price;
		$TOTAL += number_format($price, 2);
	}
}
$cant = 1;
if (count($articulos["inventario"]) > 0) {
	foreach ($articulos['inventario'] as $art => $key) {
		$pdf->Cell(20, 5, $cant, 0, 0, 'C');
		$pdf->Cell(70, 5, $key['nombre'], 0);
		$pdf->Cell(30, 5, $key['cantidad'], 0, 0, 'C');
		$pdf->Cell(30, 5, number_format($key['precio'], 2), 0, 1, 'R');
		$cant++;
	}
}
if (count($articulos['boton']) > 0) {
	foreach ($articulos['boton'] as $art => $key) {
		$pdf->Cell(20, 5, $cant, 0, 0, 'C');
		$pdf->Cell(70, 5, $key['nombre'], 0);
		$pdf->Cell(30, 5, $key['cantidad'], 0, 0, 'C');
		$pdf->Cell(30, 5, number_format($key['precio'], 2), 0, 1, 'R');
		$cant++;
	}
}

$pdf->Ln(5);

$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Ln(2);
foreach ($metodo as $met => $key) {
	if ($precio[$met] > 0) {
		$pdf->Cell(30, 5, $key, 0, 0);
		$pdf->Cell(20);
		$pdf->Cell(30, 5, '$' . number_format($precio[$met], 2), 0, 1);
	}
}
$pdf->SetFont('Times', 'B', 12);

$pdf->Cell(50);
$pdf->Cell(30, 3, '', 'B', 1);
$pdf->Cell(30, 5, 'Total', 0, 0);
$pdf->Cell(20);
$pdf->Cell(30, 5, '$' . number_format($TOTAL, 2), 0, 1);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);






$pdf->Output();
