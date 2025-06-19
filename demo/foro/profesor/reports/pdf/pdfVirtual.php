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

$virtualId = $_POST['id'];
// virtual info
$virtual = DB::table('virtual')
   ->where('id', $virtualId)->first();
// Class info
// $desc = (__COSEY) ? "descripcion" : "desc1";
$class = DB::table('padres', !__COSEY)->select("curso,descripcion")
   ->where([
      ['year', $teacher->info('year')],
      ['curso', $virtual->curso]
   ])->first();
// students info
$students = new Student();
$students = $students->findByClass($virtual->curso);

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle("Tarea de $class->descripcion",true);
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, utf8_decode($virtual->titulo), 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->splitCells("Curso: $class->curso - $class->descripcion", $teacher->fullName());
// table header
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
$pdf->Cell(60, 7, "Apellidos", 1, 0, "C", true);
$pdf->Cell(45, 7, "Nombre", 1, 0, "C", true);
$pdf->Cell(25, 7, utf8_decode("Asistió"), 1, 0, "C", true);
$pdf->Cell(25, 7, "Fecha", 1, 0, "C", true);
$pdf->Cell(25, 7, "Hora", 1, 1, "C", true);

$pdf->SetFont('Arial', '', 10);

$num = 1;
foreach ($students as $student) {
   $asisVirtual = DB::table('asistencia_virtual')->where([
      ['id_virtual', $virtualId],
      ['ss_estudiante', $student->ss]
   ])->first();

   $pdf->Cell(10, 5, $num, 1, 0, "R");
   $pdf->Cell(60, 5, ucwords(utf8_decode($student->apellidos)), 1);
   $pdf->Cell(45, 5, ucwords(utf8_decode($student->nombre)), 1);
   $pdf->Cell(25, 5, ($asisVirtual ? 'si' : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($asisVirtual ? Util::formatDate($asisVirtual->fecha) : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($asisVirtual ? Util::formatTime($asisVirtual->hora) : ''), 1, 0, "C");
   $pdf->Ln();
   $num++;
}

$pdf->Output();
