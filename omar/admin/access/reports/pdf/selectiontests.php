<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Informe sobre pruebas de selección", "Report on selection tests"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['AGOSTO', 'AUGUST'],
    ['PESO', 'WEIGHT'],
    ['ESTATURA', 'HEIGHT'],
    ['MAYO', 'MAY'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['ENERO', 'JANUARY'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['S.S.', 'S.S.'],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$teacherClass = new Teacher();
$studentClass = new Student();

$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle(utf8_encode($lang->translation("Informe sobre pruebas de selección")) . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, utf8_encode($lang->translation("Informe sobre pruebas de selección")) . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');
    $pdf->splitCells($lang->translation("Maestro(a):") . " $nom $ape", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, '', 1, 0, 'C', true);
    $pdf->Cell(85, 10, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
    $pdf->Cell(40, 5, $lang->translation("ESTATURA"), 1, 0, 'C', true);
    $pdf->Cell(60, 5, $lang->translation("PESO"), 1, 1, 'C', true);
    $pdf->Cell(10, 5, '', 0, 0, 'C');
    $pdf->Cell(85, 5, '', 0, 0, 'C');

    $pdf->Cell(20, 5, $lang->translation("AGOSTO"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("MAYO"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("AGOSTO"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("ENERO"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("MAYO"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        $gender = Util::gender($student->genero);
        $genderCount[$gender]++;
        $genderCount['T']++;
        $pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
        $pdf->Cell(85, 5, $student->apellidos . ' ' . $student->nombre, 1);
        $pdf->Cell(20, 5, '', 1, 0);
        $pdf->Cell(20, 5, '', 1, 0);
        $pdf->Cell(20, 5, '', 1, 0);
        $pdf->Cell(20, 5, '', 1, 0);
        $pdf->Cell(20, 5, '', 1, 1);
    }
    $pdf->Ln(2);
    $pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['T'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');
}


$pdf->Output();
