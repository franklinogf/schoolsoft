<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use App\Models\Teacher;
use App\Models\VirtualClass;
use Classes\PDF;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;


use Classes\Util;

Session::is_logged();

$teacher = Teacher::query()->findOrFail(Session::id());

$virtualId = $_POST['id'];

$virtual = VirtualClass::findOrFail($virtualId);

$students = Student::byClass($virtual->curso)->get();

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle("Tarea de {$virtual->subject->descripcion}", true);
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, $virtual->titulo, 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->splitCells("Curso: {$virtual->subject->display_label}", $teacher->fullName);
// table header
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
$pdf->Cell(60, 7, "Apellidos", 1, 0, "C", true);
$pdf->Cell(45, 7, "Nombre", 1, 0, "C", true);
$pdf->Cell(25, 7, "Asistió", 1, 0, "C", true);
$pdf->Cell(25, 7, "Fecha", 1, 0, "C", true);
$pdf->Cell(25, 7, "Hora", 1, 1, "C", true);

$pdf->SetFont('Arial', '', 10);

$num = 1;
foreach ($students as $student) {
   $asisVirtual = DB::table('asistencia_virtual')->where([
      'id_virtual' => $virtualId,
      'ss_estudiante' => $student->ss,
   ])->first();

   $pdf->Cell(10, 5, $num, 1, 0, "R");
   $pdf->Cell(60, 5, $student->apellidos, 1);
   $pdf->Cell(45, 5, $student->nombre, 1);
   $pdf->Cell(25, 5, ($asisVirtual ? 'si' : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($asisVirtual ? Util::formatDate($asisVirtual->fecha) : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($asisVirtual ? Util::formatTime($asisVirtual->hora) : ''), 1, 0, "C");
   $pdf->Ln();
   $num++;
}

$pdf->Output();
