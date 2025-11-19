<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Lista de estudiantes para firmar", "Students signs list"],
    ["Grado", "Grade"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['Firma', 'Sign']

]);

$school = new School();
$studentClass = new Student();

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de estudiantes para firmar") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Lista de estudiantes para firmar") . " $year", 0, 1, 'C');

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(65, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(65, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(55, 5, $lang->translation("Firma"), 1, 1, 'C', true);
    $pdf->ln(2);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        $gender = Util::gender($student->genero);
        $genderCount[$gender]++;

        $pdf->Cell(10, 10, $count + 1, 0, 0, 'C');
        $pdf->Cell(65, 10, $student->apellidos, 0);
        $pdf->Cell(65, 10, $student->nombre, 0);
        $pdf->Cell(55, 10, '___________________________', 0, 1,'C');
    }
    $pdf->Ln(2);

    $pdf->Cell(55, 5, $lang->translation("Grado"). " $grade", 1, 1, 'C', true);
    $pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, sizeof($students), 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');
}




$pdf->Output();
