<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\PDF;

require_once '../app.php';

$school = new School();
$year = $school->year();
$metodo = ['1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'ID', '4' => 'Nombre', '5' => 'ATH'];

$items = DB::table('inventario')->orderBy('articulo')->get();


//Creaci&#65533;n del objeto de la clase heredada
$pdf = new PDF();
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 10, 'AJUSTE DE INVENTARIO', 0, 1, 'C');

$pdf->Cell(0, 5, "Desde {$_POST['fecha1']} Hasta {$_POST['fecha2']}", 0, 1);
$pdf->Ln(3);

$pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Tipo', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Precio', 1, 1, 'C', true);




$pdf->SetFont('Times', '', 12);
$precio = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0);
// $tdp = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0);
foreach ($items as $item) {

	// $re = mysql_query("SELECT d.id_inv,c.fecha,c.tdp,c.year,d.precio FROM compra_cafeteria_detalle as d  LEFT JOIN compra_cafeteria as c on d.id_compra = c.id WHERE d.id_inv = '$item->id2' and c.year ='$year' and (c.fecha >= '{$_POST['fecha1']}' and c.fecha <= '{$_POST['fecha2']}')");
	$purchases = DB::table('compra_cafeteria_detalle')
		->select('compra_cafeteria_detalle.id_inv,c.fecha,c.tdp,c.year,compra_cafeteria_detalle.precio')
		->join('compra_cafeteria as c', 'compra_cafeteria_detalle.id_compra', '=', 'c.id')
		->where([
			['compra_cafeteria_detalle.id_inv', $item->id2],
			['c.year', $year],
			['c.fecha', '>=', $_POST['fecha1']],
			['c.fecha', '<=', $_POST['fecha2']]
		])->get();
	foreach ($purchases as $purchase) {

		$pdf->Cell(20, 5, $item->id2, 0, 0, 'C');
		$pdf->Cell(70, 5, $item->articulo, 0);
		$pdf->Cell(30, 5, $purchase->fecha, 0, 0, 'C');
		$pdf->Cell(30, 5, $metodo[$purchase->tdp], 0, 0, 'C');
		$pdf->Cell(30, 5, $purchase->precio, 0, 1, 'C');

		$precio[$purchase->tdp] += $purchase->precio;
	}
}



$pdf->Ln(5);
$TOTAL = 0;
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Ln(2);
foreach ($metodo as $met => $key) {
	if ($precio[$met] > 0) {
		$pdf->Cell(30, 5, $key, 0, 0);
		$pdf->Cell(20);
		$pdf->Cell(30, 5, '$' . number_format($precio[$met], 2), 0, 1);
		$TOTAL += number_format($precio[$met], 2);
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
