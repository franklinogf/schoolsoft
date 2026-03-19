<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Subject;
use Classes\PDF;

$pdf = new PDF;

$pdf->SetTitle('LISTA DE CURSOS');
$pdf->Fill();
$pdf->addPage();

$courses = Subject::with(['teacher'])->get();


$pdf->Cell(0, 10, 'LISTA DE CURSOS', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(7, 5, '  ', 1, 0, 'C', true);
$pdf->Cell(65, 5, 'Profesor(a)', 1, 0, 'C', true);
$pdf->Cell(18, 5, 'Curso', 1, 0, 'C', true);
$pdf->Cell(50, 5, 'Descripción', 1, 0, 'C', true);
$pdf->Cell(10, 5, 'Crs.', 1, 0, 'C', true);
$pdf->Cell(10, 5, 'Peso', 1, 0, 'C', true);
$pdf->Cell(34, 5, 'Horario', 1, 1, 'C', true);
$pdf->SetFont('Times', '', 10);
foreach ($courses as $index => $course) {

    $teacher = $course->teacher;

    $pdf->Cell(7, 5, $index + 1, 1);
    $pdf->Cell(65, 5, $teacher ? "$teacher->apellidos $teacher->nombre" : '', 1);
    $pdf->Cell(18, 5, $course->curso, 1);
    $pdf->Cell(50, 5, $course->desc1, 1);
    $pdf->Cell(10, 5, number_format($course->credito, 2), 1, 0, 'R');
    $pdf->Cell(10, 5, number_format($course->peso, 2), 1, 0, 'R');
    $pdf->Cell(34, 5, "$course->entrada - $course->salida", 1, 1);
}

$pdf->Output();
