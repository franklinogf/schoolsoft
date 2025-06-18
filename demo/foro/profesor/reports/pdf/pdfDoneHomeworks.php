<?php
require_once '../../../../app.php';

use Classes\Controllers\Student;
use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$teacher = new Teacher(Session::id());

$homeworkId = $_POST['id'];
// Homework info
$homework = DB::table('tbl_documentos', !__COSEY)
   ->where('id_documento', $homeworkId)->first();
// Class info
// $desc = (__COSEY) ? "descripcion" : "desc1";
$class = DB::table('padres', !__COSEY)->select("curso,descripcion")
   ->where([
      ['year', $teacher->info('year')],
      ['curso', $homework->curso]
   ])->first();
// students info
$students = new Student();
$students = $students->findByClass($homework->curso);

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle("Tarea de $class->descripcion",true);
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, utf8_decode($homework->titulo), 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->splitCells("Curso: $class->curso - $class->descripcion", $teacher->fullName());
// table header
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
$pdf->Cell(60, 7, "Apellidos", 1, 0, "C", true);
$pdf->Cell(45, 7, "Nombre", 1, 0, "C", true);
$pdf->Cell(25, 7, "Entregado", 1, 0, "C", true);
$pdf->Cell(25, 7, "Fecha", 1, 0, "C", true);
$pdf->Cell(25, 7, "Hora", 1, 1, "C", true);

$pdf->SetFont('Arial', '', 10);

$num = 1;
foreach ($students as $student) {
   $doneHomework = DB::table('tareas_enviadas', !__COSEY)->where([
      ['id_tarea', $homeworkId],
      ['id_estudiante', $student->mt]
   ])->first();

   $pdf->Cell(10, 5, $num, 1, 0, "R");
   $pdf->Cell(60, 5, ucwords(utf8_decode($student->apellidos)), 1);
   $pdf->Cell(45, 5, ucwords(utf8_decode($student->nombre)), 1);
   $pdf->Cell(25, 5, ($doneHomework ? 'si' : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($doneHomework ? Util::formatDate($doneHomework->fecha) : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($doneHomework ? Util::formatTime($doneHomework->hora) : ''), 1, 0, "C");
   $pdf->Ln();
   $num++;
}

$pdf->Output();
