<?php
require_once('../../control.php');
require_once('../../../fpdf16/fpdf.php');


$ID_COMPRA = $id_compra;
// $ID_COMPRA = 181;
// $year = '14-15';
$rasa = mysql_query("SELECT * FROM compra_cafeteria WHERE year = '$year' and id = $ID_COMPRA");
$compras = mysql_fetch_object($rasa);


$pdf = new FPDF();
$pdf->SetAutoPageBreak(true, 5);
$pdf->AliasNbPages();
$pdf->AddPage();
$dat = "select * from colegio where usuario = 'administrador'";
$tab = mysql_query($dat, $con) or die("problema con query");
$row = mysql_fetch_row($tab);

//Logo
$pdf->Image('../../logo/logo.gif', 10, 10, 25);
//Arial bold 15
$pdf->SetFont('Arial', 'B', 15);
//Movernos a la derecha
$sp = 80;
$pdf->Cell($sp);
//Ttulo
$pdf->Cell(30, 5, $row[0], 0, 1, 'C');
if ($row[52] == 'SI') {
	$pdf->Cell(80);
	$pdf->Cell(30, 8, $row[44], 0, 1, 'C');
}
$pdf->SetFont('Arial', '', 9);
//Movernos a la derecha
$pdf->Ln(2);
$pdf->Cell($sp);
$pdf->Cell(30, 2, $row[1], 0, 0, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->Cell(30, 2, $row[2], 0, 1, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->Cell(30, 2, $row[3] . ', ' . $row[4] . ' ' . $row[5], 0, 0, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(30, 3, 'Tel. ' . $row[12] . ' Fax ' . $row[13], 0, 0, 'C');
$pdf->Ln(3);
$pdf->Cell($sp);
$pdf->Cell(30, 3, $row[20], 0, 0, 'C');
//Salto de lnea
$pdf->Ln(10);
$pdf->Cell($sp);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 10, 'RECIBO DE COMPRA', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFillColor(230);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(230);

$pdf->Cell(0, 5, "$compras->nombre $compras->apellido $compras->grado", 0, 1);
$pdf->Cell(0, 5, "Recibo #{$ID_COMPRA}", 0, 1, 'R');
$pdf->Cell(70, 5, utf8_decode('Artículo'), 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Precio', 1, 1, 'C', true);




$pdf->SetFont('Times', '', 12);

$ree = mysql_query("SELECT * FROM compra_cafeteria_detalle WHERE `id_compra` =$ID_COMPRA");
while ($art = mysql_fetch_assoc($ree)) {
	$pdf->Cell(70, 5, utf8_decode($art['descripcion']), 0);
	$pdf->Cell(30, 5, $compras->fecha, 0, 0, 'C');
	$pdf->Cell(30, 5, $art['precio'], 0, 1, 'C');
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

$r = mysql_query("SELECT cantidad from year where ss = '$compras->ss' and year = '$year'");
$deposito = mysql_fetch_object($r);

$pdf->Cell(0, 5, "Balance disponible $" . $deposito->cantidad);




$pdfoutputfile = 'temp-folder/temp-file.pdf';
$pdfdoc = $pdf->Output($pdfoutputfile, 'F');
$pdf->Close();
// $pdf->Output();
