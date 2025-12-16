<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\PDF;

require_once __DIR__ . '/../app.php';

$school = new School();
$year = $school->year();

$metodo = ['1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'ID', '4' => 'Nombre', '5' => 'ATH'];

$purchases = DB::table('compra_cafeteria')->where([
	['year', $year],
	['fecha', '>=', $_POST['fecha1']],
	['fecha', '<=', $_POST['fecha2']]
])->orderBy('fecha')->get();
//Creaci&#65533;n del objeto de la clase heredada
$pdf = new PDF();
$pdf->Fill();
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage('L');
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 10, 'CUADRE DEL DIA DETALLADO', 0, 1, 'C');
$pdf->Ln(5);


$pdf->Cell(0, 5, "Desde {$_POST['fecha1']} Hasta {$_POST['fecha2']}", 0, 1);
$pdf->Ln(3);

$pdf->Cell(15, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'TDP', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Precio', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'ID Est.', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'Nombre Estudiante', 1, 1, 'C', true);


$pdf->SetFont('Times', '', 12);
$precio = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
// $tdp = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0);
$TOTAL = 0;
foreach ($purchases as $purchase) {

	$purchaseItems = DB::table('compra_cafeteria_detalle')->where('id_compra', $purchase->id)->get();
	foreach ($purchaseItems as $purchaseItem) {
		$price = $purchaseItem->precio_final ? $purchaseItem->precio_final : $purchaseItem->precio;
		$pdf->Cell(15, 5, $purchase->id, 0, 0, 'C');
		$pdf->Cell(70, 5, $purchaseItem->descripcion, 0);
		$pdf->Cell(25, 5, $purchase->fecha, 0, 0, 'C');
		$pdf->Cell(30, 5, $metodo[$purchase->tdp] ?? '', 0, 0, 'C');
		$pdf->Cell(25, 5, number_format($price, 2), 0, 0, 'C');
		$pdf->Cell(25, 5, $purchase->ss, 0, 0, 'C');
		$pdf->Cell(70, 5, "$purchase->apellido $purchase->nombre", 0, 1, 'L');

		if (isset($precio[$purchase->tdp])) {

			$precio[$purchase->tdp] += number_format($price, 2);
		}
		$TOTAL += number_format($price, 2);
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
