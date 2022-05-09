<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();


$teacher = new Teacher(Session::id());

$pdf = new PDF();
$pdf->SetTitle('Clasificación de notas', true);
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetAutoPageBreak(true, 10);
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 17);
$pdf->Cell(0,5,utf8_decode('Informe de Clasificación de Notas'),0,1,'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0,5,utf8_decode("GRADO $teacher->grado / AÑO {$teacher->info('year')}"),0,1,'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(90,7,'Nombre del estudiante',1,0,'C',true);
$pdf->Cell(8,7,'A-1',1,0,'C',true);
$pdf->Cell(8,7,'B-1',1,0,'C',true);
$pdf->Cell(8,7,'C-1',1,0,'C',true);
$pdf->Cell(8,7,'A-2',1,0,'C',true);
$pdf->Cell(8,7,'B-2',1,0,'C',true);
$pdf->Cell(8,7,'C-2',1,0,'C',true);
$pdf->Cell(8,7,'A-3',1,0,'C',true);
$pdf->Cell(8,7,'B-3',1,0,'C',true);
$pdf->Cell(8,7,'C-3',1,0,'C',true);
$pdf->Cell(8,7,'A-4',1,0,'C',true);
$pdf->Cell(8,7,'B-4',1,0,'C',true);
$pdf->Cell(8,7,'C-4',1,0,'C',true);
$pdf->Cell(15,7,'PROM',1,1,'C',true);



$pdf->Output();
