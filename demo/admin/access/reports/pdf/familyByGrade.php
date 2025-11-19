<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Util;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();

$lang = new Lang([
    ["Informe de familia por grado", "Family report by grade"],
    ["Grado", "Grade"],
    ['Estudiante', 'Student'],
    ['Cuenta', 'Account'],
    
]);

$school = new School();
$studentClass = new Student();

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de familia por grado") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de familia por grado") . " $year", 0, 1, 'C');
    $students = $studentClass->findByGrade($grade);

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(30, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Estudiante"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Grado"), 1, 1, 'C', true);
    $pdf->ln(2);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
        $pdf->Cell(30, 5, $student->id, 0,0,'R');
        $pdf->Cell(70, 5, "$student->nombre $student->apellidos", 0);
        $pdf->Cell(70, 5, $student->grado, 0, 1,'C');
    }
}




$pdf->Output();
