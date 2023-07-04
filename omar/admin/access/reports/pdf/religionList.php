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
    ['Lista de religión', 'Religion List'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Religión', 'Religion'],
    ['Parroquia o Iglesia', 'Parish or Church'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Edad', 'Age'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year');

$unegrade = $_POST['grade'];

if ($unegrade !=='')
   {
   $allGrades = [$unegrade];
   }
else
   {
   $allGrades = $school->allGrades();
   }


$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de religión"). " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $count = 1;
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Lista de religión") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(80, 5, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Religión"), 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Parroquia o Iglesia"), 1, 1, 'C', true);
    $col=1;$wo2=65;
    foreach ($students as $count => $student) {
      $pdf->Cell(10, 5, $count, 1, 0,'R');
      $pdf->Cell(15, 5, $student->id, 1, 0);
      $pdf->Cell(80, 5, utf8_decode($student->apellidos.' '.$student->nombre), 1,0);
      $pdf->Cell(45, 5, utf8_decode($student->religion), 1, 0);
      $pdf->Cell(45, 5, utf8_decode($student->iglesia), 1, 1);
         $count++;
    }
}

$pdf->Output();
