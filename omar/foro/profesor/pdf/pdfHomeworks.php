<?php
require_once '../../../app.php';

use Classes\Controllers\Homework;
use Classes\Controllers\Student;
use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$teacher = new Teacher(Session::id());
// $hw = new Homework();
$class = $_GET['class'];
// $homeworks = $hw->findByTeacher(Session::id(),$class);
// Homework info
$homeworks = $teacher->homeworks($class,false);
// Class info
$class = DB::table('cursos')
   ->where([
      ['year', $teacher->info('year')],
      ['curso', $class]
   ])->first();


$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle("Tareas de $class->desc1");
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, "$class->curso - $class->desc1", 0, 1, 'C');
$pdf->Ln(3);

// table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
$pdf->Cell(80, 7, "Titulo", 1, 0, "C", true);
$pdf->Cell(35, 7, "Fecha de inicio", 1, 0, "C", true);
$pdf->Cell(25, 7, "Hora", 1, 0, "C", true);
$pdf->Cell(35, 7, "Fecha de cierre", 1, 1, "C", true);

$pdf->SetFont('Arial', '', 10);

$num = 1;
foreach ($homeworks as $homework) {

   $pdf->Cell(10, 5, $num, 1, 0, "R");
   $pdf->Cell(80, 5, $homework->titulo, 1);
   $pdf->Cell(35, 5, Util::formatDate($homework->fec_in), 1, 0, 'C');
   $pdf->Cell(25, 5, Util::formatTime($homework->hora), 1, 0, "C");
   $pdf->Cell(35, 5, Util::formatDate($homework->fec_out), 1, 0, "C");
   $pdf->Ln();
   $num++;
}

$pdf->Output();
