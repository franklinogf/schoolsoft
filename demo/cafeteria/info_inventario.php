<?php

use Classes\DataBase\DB;
use Classes\PDF;

require_once __DIR__ . '/../app.php';

$items = DB::table('inventario')->orderBy('articulo')->get();

$pdf = new PDF();
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', 'B', 15);
$pdf->Cell(0, 10, 'INFORME DE INVENTARIO', 0, 1, 'C');
$pdf->SetFont('Times', 'B', 12);

$pdf->Cell(10, 5, '#', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Precio', 1, 0, 'C', true);
$pdf->Cell(22, 5, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(22, 5, 'Minimo', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
foreach ($items as $index => $item) {
	$pdf->Cell(10, 5, $index + 1, 0, 0, 'R');
	$pdf->Cell(30, 5, $item->id, 0, 0, 'C');
	$pdf->Cell(70, 5, $item->articulo, 0, 0, 'L');
	$pdf->Cell(25, 5, number_format($item->precio, 2), 0, 0, 'R');
	$pdf->Cell(22, 5, $item->cantidad, 0, 0, 'C');
	$pdf->Cell(22, 5, $item->minimo, 0, 1, 'C');
}

$pdf->Output();
