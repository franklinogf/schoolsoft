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

$lang = new Lang([['Liata matrícula por salón', 'Enrollment classroom list'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
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

$year = $school->year();

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
$pdf->SetTitle($lang->translation("Liata matrícula por salón") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
  $pdf->Cell(0, 5, $lang->translation("Liata matrícula por salón") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
  $nom = $teacher->nombre ?? '';
  $ape = $teacher->apellidos ?? '';
  $pdf->splitCells($lang->translation("Maestro(a):") . " $nom $ape", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 5, '', 1, 1, 'C', true);
    $col=1;$wo2=65;
    foreach ($students as $count => $student) {
      $pdf->SetY($wo2);
      if ($col==1){$pdf->SetX(15);}
      if ($col==2){$pdf->SetX(80);}
      if ($col==3){$pdf->SetX(145);}
      $pdf->Cell(50, 5, $student->apellidos, 'LRT',1);
      if ($col==1){$pdf->SetX(15);}
      if ($col==2){$pdf->SetX(80);}
      if ($col==3){$pdf->SetX(145);}
      $pdf->Cell(50, 5, $student->nombre, 'RLB', 1);
      $pdf->SetY($wo2);
      if ($col==3){$col=0;$wo2=$wo2+15;}
      $col=$col+1;
    }
}

$pdf->Output();
