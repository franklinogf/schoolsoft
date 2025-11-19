<?php

use Classes\DataBase\DB;
use Classes\PDF;

require_once __DIR__ . '/../../app.php';



$ID_COMPRA = $id_compra;
$purchase = DB::table('compra_cafeteria_detalle')
	->where([['year',$year],['id_compra', $ID_COMPRA]])
	->first();


$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 5);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 10, 'RECIBO DE COMPRA', 0, 1, 'C');
$pdf->Ln(8);


$pdf->SetFont('Times', 'B', 12);

$pdf->Cell(0, 5, "$purchase->nombre $purchase->apellido $purchase->grado", 0, 1);
$pdf->Cell(0, 5, "Recibo #{$ID_COMPRA}", 0, 1, 'R');
$pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Precio', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 12);

$details = DB::table('compra_cafeteria_detalle')
	->where('id_compra', $ID_COMPRA)
	->get();
foreach($details as $detail){
	$pdf->Cell(70, 5, $art->descripcion, 0);
	$pdf->Cell(30, 5, $purchase->fecha, 0, 0, 'C');
	$pdf->Cell(30, 5, $art->precio, 0, 1, 'C');
}

$pdf->Ln(5);
$TOTAL = 0;
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Ln(2);
if ($purchase->pago1 > 0) {
	$pdf->Cell(30, 5, 'Pago con deposito');
	$pdf->Cell(20);
	$pdf->Cell(30, 5, '$' . number_format($purchase->pago1, 2), 0, 1);
}
if ($purchase->pago2 > 0) {
	$pdf->Cell(30, 5, "Pago con " . $purchase->tdp2);
	$pdf->Cell(20);
	$pdf->Cell(30, 5, '$' . number_format($purchase->pago2, 2), 0, 1);
}

$pdf->SetFont('Times', 'B', 12);

$pdf->Cell(50);
$pdf->Cell(30, 3, '', 'B', 1);
$pdf->Cell(30, 5, 'Total', 0, 0);
$pdf->Cell(20);
$pdf->Cell(30, 5, '$' . number_format($purchase->total, 2), 0, 1);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);

$deposit = DB::table('year')
	->select('cantidad')
	->where([['ss', $purchase->ss], ['year', $year]])
	->first();

$pdf->Cell(0, 5, "Balance disponible $" . $deposit->cantidad);


$pdfdoc = $pdf->Output('S');
$pdf->Close();
// $pdf->Output();
