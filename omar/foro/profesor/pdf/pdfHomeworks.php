<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

$teacher = new Teacher(Session::id());

$homeworkId = $_GET['id'];
// Homework info
$homework = DB::table('tbl_documentos')
   ->where('id_documento', $homeworkId)->first();
// Class info
$class = DB::table('cursos')
->where([
   ['year', $teacher->info('year')],
   ['curso', $homework->curso]
])->first();

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle("Tarea de $class->desc1");
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0,5,$homework->titulo,0,1,'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->splitCells("Curso: $class->curso - $class->desc1",$teacher->fullName());
// table header
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
$pdf->Cell(60, 7, "Apellidos", 1, 0, "C", true);
$pdf->Cell(45, 7, "Nombre", 1, 0, "C", true);
$pdf->Cell(25, 7, "Entregado", 1, 0, "C", true);
$pdf->Cell(25, 7, "Fecha", 1, 0, "C", true);
$pdf->Cell(25, 7, "Hora", 1, 0, "C", true);

$pdf->SetFont('Arial', '', 10);

// $num = 1;
// foreach ($students as $student) {

//    $pdf->Cell(10, 5, $num, 1, 0, "R");
//    $pdf->Cell(15, 5, $student->id, 1, 0, "C");
//    $pdf->Cell(60, 5, ucwords(utf8_decode($student->apellidos)), 1);
//    $pdf->Cell(40, 5, ucwords(utf8_decode($student->nombre)), 1);
//    $pdf->Cell(35, 5, $student->usuario, 1, 0, "C");
//    $pdf->Cell(35, 5, $student->clave, 1, 0, "C");
//    $pdf->Ln();
//    $num++;
// }

$pdf->Output();
