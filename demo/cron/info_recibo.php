<?php
require_once __DIR__ . '/../app.php';

use App\Models\CafeteriaOrder;
use Classes\PDF;

$ID_COMPRA = $id_compra;
$CANTIDAD = $cantidad;

$rasa = CafeteriaOrder::find($ID_COMPRA);

$compras = $rasa;
$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 10, 'RECIBO DE COMPRA', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFillColor(230);
$pdf->SetFont('Times', 'B', 12);


$pdf->Cell(0, 5, "$compras->nombre $compras->apellido $compras->grado", 0, 1);
$pdf->Cell(0, 5, "Recibo #{$ID_COMPRA}", 0, 1, 'R');
$pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
// $pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Precio', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 12);
$t = 0;
foreach ($compras->items as $art) {
    $pdf->Cell(70, 5, $art->descripcion, 0);
    $pdf->Cell(30, 5, $compras->fecha, 0, 0, 'C');
    $pdf->Cell(30, 5, $art->precio_final > 0 ? $art->precio_final : $art->precio, 0, 1, 'C');
    $t += $art->precio_final > 0 ? $art->precio_final : $art->precio;
}

$pdf->Ln(5);
$TOTAL = 0;
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Ln(2);
if ($compras->pago1 > 0) {
    $pdf->Cell(30, 5, 'Pago con deposito');
    $pdf->Cell(20);
    $pdf->Cell(30, 5, '$' . number_format($compras->pago1, 2), 0, 1);
}
if ($compras->pago2 > 0) {
    $pdf->Cell(30, 5, "Pago con " . $compras->tdp2);
    $pdf->Cell(20);
    $pdf->Cell(30, 5, '$' . number_format($compras->pago2, 2), 0, 1);
}

$pdf->SetFont('Times', 'B', 12);

$pdf->Cell(50);
$pdf->Cell(30, 3, '', 'B', 1);
$pdf->Cell(30, 5, 'Total', 0, 0);
$pdf->Cell(20);
$pdf->Cell(30, 5, '$' . number_format($compras->total, 2), 0, 1);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Ln(2);
$pdf->Cell(0, 5, "Balance disponible al día " . $compras->fecha . " " . $CANTIDAD);

$pdfoutputfile = "$target_dir/temp-file.pdf";
$pdfdoc = $pdf->Output($pdfoutputfile, 'F');
$pdf->Close();
