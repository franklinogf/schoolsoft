<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\PDF;

require_once '../app.php';

$school = new School();
$year = $school->year();


$stocks = DB::table('inventario')->get();


$pdf = new PDF();
$pdf->Fill();
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 10, 'INFORME DE COMPRAS', 0, 1, 'C');

$pdf->Ln(3);

$pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Precio', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'Minimo', 1, 0, 'C', true);
$pdf->Cell(40, 5, 'Codigo de barra', 1, 0, 'C', true);
$pdf->Cell(17, 5, 'Comprar', 1, 1, 'C', true);


foreach ($stocks as $stock) {
	$pdf->SetFont('Times', '', 12);

	if ($stock->cantidad <= $stock->minimo) {
		$pdf->Cell(20, 10, $stock->id2, 1, 0, 'C');
		$pdf->Cell(70, 10, $stock->articulo, 1);
		$pdf->Cell(20, 10, $stock->precio, 1, 0, 'R');
		$pdf->Cell(20, 10, $stock->cantidad, 1, 0, 'C');
		$pdf->Cell(15, 10, $stock->minimo, 1, 0, 'C');
		$pdf->Codabar($pdf->GetX(), $pdf->GetY(), $stock->cbarra, '*', '*', 0.195, 5);
		$pdf->Cell(40, 10, '', 1);
		$pdf->Cell(17, 10, '', 1, 1);
	}
}


$pdf->Output();
