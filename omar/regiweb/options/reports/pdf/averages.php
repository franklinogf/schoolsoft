<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$grade = $teacher->grado;
$students = new Student();
$students = $students->findByGrade($grade);


$pdf = new PDF();
$pdf->addPage();
$pdf->Fill();
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 5, utf8_decode("GRADO $teacher->grado"), 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(10, 5, ' ', 1, 0, 'C', true);
$pdf->Cell(60, 5, 'Apellidos', 1, 0, 'C', true);
$pdf->Cell(50, 5, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(22, 5, 'S-1', 1, 0, 'C', true);
$pdf->Cell(22, 5, 'S-2', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'FINAL', 1, 1, 'C', true);
$count = 1;
foreach ($students as $student) {
    $father = DB::table('padres')->Where([
        ['ss', $student->ss],
        ['year', $year]
    ])->first();
    $pdf->Cell(10, 5, $count, 1, 0, 'C');
    $pdf->Cell(60, 5, $student->apellidos, 1);
    $pdf->Cell(50, 5, $student->nombre, 1);
    $pdf->Cell(22, 5, $student->tr1, 1, 0, 'C');
    $pdf->Cell(22, 5, $student->tr2, 1, 0, 'C');
    $pdf->Cell(20, 5, $student->fin, 1, 1, 'C');
    $count++;
}
$pdf->Output();
