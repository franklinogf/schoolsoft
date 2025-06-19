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
    ["Lista total por grado", "Total list by grade"],
    ["Maestro(a):", "Teacher:"],
    ["Grado", "Grade"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['Nuevos', 'New'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista total por grado"). " $year", true);
$pdf->Fill();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista total por grado") . " $year", 0, 1, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 5, '', 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Nuevos"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Total', 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Grado"), 1, 1, 'C', true);
$tnuevos=0;
$tf=0;
$tm=0;
$gt=0;
foreach ($allGrades as $grade) {
    $nuevos=0;
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        $gender = Util::gender($student->genero);
        $genderCount[$gender]++;
        $genderCount['T']++;
        if ($student->nuevo == 'Si'){$nuevos=$nuevos+1;}
    }
    $pdf->Cell(15, 5, $count + 1, 1, 0, 'C');
    $pdf->Cell(30, 5, $nuevos, 1, 0, 'C');
    $pdf->Cell(30, 5, $genderCount['F'], 1, 0, 'C');
    $pdf->Cell(30, 5, $genderCount['M'], 1, 0, 'C');
    $pdf->Cell(30, 5, $genderCount['T'], 1, 0, 'C');
    $pdf->Cell(30, 5, $grade, 1, 1, 'C');
    $tnuevos=$tnuevos+$nuevos;
    $tf=$tf+$genderCount['F'];
    $tm=$tm+$genderCount['M'];
    $gt=$gt+$genderCount['T'];
}
    $pdf->Cell(15, 5, 'Total', 1, 0, 'C', true);
    $pdf->Cell(30, 5, $tnuevos, 1, 0, 'C', true);
    $pdf->Cell(30, 5, $tf, 1, 0, 'C', true);
    $pdf->Cell(30, 5, $tm, 1, 0, 'C', true);
    $pdf->Cell(30, 5, $gt, 1, 0, 'C', true);
    $pdf->Cell(30, 5, '', 1, 1, 'C', true);

$pdf->Output();
