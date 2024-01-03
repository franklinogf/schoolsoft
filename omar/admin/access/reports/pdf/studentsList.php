<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();

$lang = new Lang([
    ["Lista de estudiantes", "Students list"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Grado', 'Grade'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],

]);

$school = new School();
$studentClass = new Student();
$students = $studentClass->all();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de estudiantes") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de estudiantes") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 5, '', 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
$pdf->Cell(65, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(65, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(15, 5, $lang->translation("Grado"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);

foreach ($students as $count => $student) {
    $pdf->Cell(15, 5, $count + 1, 1, 0, 'C');
    $pdf->Cell(20, 5, $student->id, 1, 0, 'R');
    $pdf->Cell(65, 5, $student->apellidos, 1);
    $pdf->Cell(65, 5, $student->nombre, 1);
    $pdf->Cell(15, 5, $student->grado, 1, 1, 'C');
}




$pdf->Output();
