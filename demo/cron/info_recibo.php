<?php
use Fpdf\Fpdf;
require_once dirname(__DIR__, 1) . '/vendor/fpdf/fpdf/src/Fpdf/Fpdf.php';
$ID_COMPRA = $id_compra;
$CANTIDAD = $cantidad;

$rasa = $mysqli->query("SELECT * FROM compra_cafeteria WHERE year = '$year' and id = $ID_COMPRA");
$compras = $rasa->fetch_object();

$pdf = new Fpdf();
$pdf->SetAutoPageBreak(true, 5);
$pdf->AliasNbPages();
$pdf->AddPage();
$dat = "select * from colegio where usuario = 'administrador'";
$tab = $mysqli->query($dat);
$row = $tab->fetch_assoc();

$pdf->Image('https://schoolsoftpr.org/cbl/logo/logo.gif', 10, 10, 25);
$pdf->SetFont('Arial', 'B', 15);
$sp = 80;
$pdf->Cell($sp);
$pdf->Cell(30, 5, $row['colegio'], 0, 1, 'C');
// if ($row[52] == 'SI') {
//     $pdf->Cell(80);
//     $pdf->Cell(30, 8, $row['logo'], 0, 1, 'C');
// }
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(2);
$pdf->Cell($sp);
$pdf->Cell(30, 2, $row['dir1'], 0, 0, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->Cell(30, 2, $row['dir2'], 0, 1, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->Cell(30, 2, $row['pueblo1'] . ', ' . $row['esta1'] . ' ' . $row['esta1'], 0, 0, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(30, 3, 'Tel. ' . $row['telefono'] . ' Fax ' . $row['fax'], 0, 0, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->Cell(30, 3, $row['idioma'], 0, 0, 'C');
$pdf->Ln(10);
$pdf->Cell($sp);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 10, 'RECIBO DE COMPRA', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFillColor(230);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(230);

$pdf->Cell(0, 5, utf8_decode("$compras->nombre $compras->apellido $compras->grado"), 0, 1);
$pdf->Cell(0, 5, "Recibo #{$ID_COMPRA}", 0, 1, 'R');
$pdf->Cell(70, 5, utf8_decode('Artículo'), 1, 0, 'C', true);
// $pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Precio', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 12);
$t = 0;
$ree = $mysqli->query("SELECT * FROM compra_cafeteria_detalle WHERE `id_compra` =$ID_COMPRA");
while ($art = $ree->fetch_assoc()) {
    $pdf->Cell(70, 5, $art['descripcion'], 0);
    $pdf->Cell(30, 5, $compras->fecha, 0, 0, 'C');
    $pdf->Cell(30, 5, $art['precio_final'] > 0 ? $art['precio_final'] : $art['precio'], 0, 1, 'C');
    $t += $art['precio_final'] > 0 ? $art['precio_final'] : $art['precio'];
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
$pdf->Cell(0, 5, utf8_decode("Balance disponible al día ") . $compras->fecha . " " . $CANTIDAD);

$pdfoutputfile = "$target_dir/temp-file.pdf";
$pdfdoc = $pdf->Output($pdfoutputfile, 'F');
$pdf->Close();
