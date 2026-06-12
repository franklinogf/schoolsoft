<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Homework;
use App\Models\Student;
use App\Models\Teacher;
use Classes\PDF;
use Classes\Session;
use Classes\Util;
use Illuminate\Database\Capsule\Manager as DB;

Session::is_logged();

$homeworkId = $_POST['id'];
// Homework info
$homework = Homework::query()->with('teacher')->findOrFail($homeworkId);

$students = Student::byClass($homework->curso)->get();

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle("Tarea de {$homework->subject->descripcion}", true);
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, $homework->titulo, 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->splitCells("Curso: {$homework->subject->display_label}", $homework->teacher->fullName);
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
   $doneHomework = DB::table('tareas_enviadas')->where([
      'id_tarea' => $homeworkId,
      'id_estudiante' => $student->mt
   ])->first();

   $pdf->Cell(10, 5, $num, 1, 0, "R");
   $pdf->Cell(60, 5, $student->apellidos, 1);
   $pdf->Cell(45, 5, $student->nombre, 1);
   $pdf->Cell(25, 5, ($doneHomework ? 'si' : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($doneHomework ? Util::formatDate($doneHomework->fecha) : ''), 1, 0, "C");
   $pdf->Cell(25, 5, ($doneHomework ? Util::formatTime($doneHomework->hora) : ''), 1, 0, "C");
   $pdf->Ln();
   $num++;
}

$pdf->Output();
