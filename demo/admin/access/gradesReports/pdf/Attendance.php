<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ['Ausencia diaria', 'Daily absence'],
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
    ['Tarde', 'Late'],
    ['Ausencia', 'Absence'],
   ['Ausencia-situación en el hogar', 'Absence-situation at home'],
   ['Ausencia-determinación del hogar(viaje)', 'Absence-determination of home (travel)'],
    ['Ausencia-actividad con padres(open house)', 'Absence-activity with parents (open house)'],
    ['Ausencia-enfermedad', 'Absence-illness'],
    ['Ausencia-cita', 'Absence-appointment'],
    ['Ausencia-actividad educativa del colegio', 'Absence-school educational activity'],
    ['Ausencia-sin excusa del hogar', 'Unexcused absence from home'],
    ['Tardanza-sin excusa del hogar', 'Unexcused tardiness home'],
   ['Tardanza-situación en el hogar', 'Late situation at home'],
   ['Tardanza-problema en la transportación', 'Transportation delay problem.'],
    ['Tardanza-enfermedad', 'Delay due to illness'],
    ['Tardanza-cita', 'Late - appointment'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();
$seasis = $_POST['seasis'];
$grado = $_POST['grado'];
$lista = $_POST['lista'];
$ss = $_POST['teacher'] ?? '';
$year = $school->info('year2');
$aus = [];
$aus[1] = $lang->translation('Ausencia-situaci&#65533;n en el hogar');
$aus[2] = $lang->translation('Ausencia-determinaci&#65533;n del hogar(viaje)');
$aus[3] = $lang->translation('Ausencia-actividad con padres(open house)');
$aus[4] = $lang->translation('Ausencia-enfermedad');
$aus[5] = $lang->translation('Ausencia-cita');
$aus[6] = $lang->translation('Ausencia-actividad educativa del colegio');
$aus[7] = $lang->translation('Ausencia-sin excusa del hogar');
$aus[8] = $lang->translation('Tardanza-sin excusa del hogar');
$aus[9] = $lang->translation('Tardanza-situaci&#65533;n en el hogar');
$aus[10] = $lang->translation('Tardanza-problema en la transportaci&#65533;n');
$aus[11] = $lang->translation('Tardanza-enfermedad');
$aus[12] = $lang->translation('Tardanza-cita');

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Ausencia diaria") . " $year", true);
$pdf->Fill();

if ($seasis == 1)
   {
   if ($grado == 'todos')
      {
      $allGrades = $school->allGrades();
      }
   else
      {
      $allGrades = [];
      $allGrades[1] = $grado;
      }

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Ausencia diaria") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
      $nom = $teacher->nombre ?? '';
      $ape = $teacher->apellidos ?? '';
      $pdf->splitCells($lang->translation("Maestro(a):") . " $nom $ape", $lang->translation("Grado:") . " $grade");
    $pdf->SetFont('Arial', 'B', 10);
    if ($lista == 1)
       {
       $pdf->Cell(8, 5, '', 1, 0, 'C', true);
       $pdf->Cell(52, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
       $pdf->Cell(42, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
       $pdf->Cell(20, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
       $pdf->Cell(70, 5, $lang->translation("Ausencia"), 1, 1, 'C', true);
       }
    else
       {
       $pdf->Cell(10, 5, '', 1, 0, 'C', true);
       $pdf->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
       $pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
       $pdf->Cell(30, 5, $lang->translation("Ausencias"), 1, 0, 'C', true);
       $pdf->Cell(30, 5, $lang->translation("Tarde"), 1, 1, 'C', true);
       }
    $pdf->SetFont('Arial', '', 10);
    $a = 0;
    foreach ($students as $student) {
        list($ss1, $ss2, $ss3) = explode("-", $student->ss);
           $asistencia = DB::table('asispp')->where([
             ['year', $year],
             ['grado', $grade],
             ['ss', $student->ss]
             ])->orderBy('fecha')->get();
           $tar = [];
           if ($seasis == 1 and $lista == 1)
              {
              foreach ($asistencia as $asis) 
                      {
                      $a = $a + 1;
                      $pdf->Cell(8, 5, $a, 1, 0, 'R');
                      $pdf->Cell(52, 5, $asis->apellidos, 1, 0, 'L');
                      $pdf->Cell(42, 5, $asis->nombre, 1, 0, 'L');
                      $pdf->Cell(20, 5, $asis->fecha, 1, 0, 'L');
                      $pdf->Cell(70, 5, utf8_encode($aus[$asis->codigo]), 1, 1, 'L');
                      }
              }
           if ($seasis == 1 and $lista == 2)
              {
              $au=0;
              $ta=0;
              foreach ($asistencia as $asis) 
                      {
                      if ($asis->codigo < 8){$au=$au+1;}
                      if ($asis->codigo > 7){$ta=$ta+1;}
                      }
              $a = $a + 1;
              $pdf->Cell(10, 5, $a, 1, 0, 'R');
              $pdf->Cell(60, 5, $student->apellidos, 1, 0, 'L');
              $pdf->Cell(50, 5, $student->nombre, 1, 0, 'L');
              $pdf->Cell(30, 5, $au, 1, 0, 'C');
              $pdf->Cell(30, 5, $ta, 1, 1, 'C');
              }
           }
    }
}

if ($seasis == 2)
   {
    $student = DB::table('year')->where([
             ['year', $year],
             ['ss', $ss]
             ])->orderBy('ss')->first();
    $teacher = $teacherClass->findByGrade($student->grado);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Ausencia diaria") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . utf8_encode(" $teacher->nombre $teacher->apellidos"), $lang->translation("Grado:") . " $student->grado");
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(8, 5, '', 1, 0, 'C', true);
    $pdf->Cell(52, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(42, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Ausencia"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $a = 0;
        list($ss1, $ss2, $ss3) = explode("-", $student->ss);
           $asistencia = DB::table('asispp')->where([
             ['year', $year],
             ['ss', $student->ss]
             ])->orderBy('fecha')->get();
           $tar = [];
              foreach ($asistencia as $asis) 
                      {
                      $a = $a + 1;
                      $pdf->Cell(8, 5, $a, 1, 0, 'R');
                      $pdf->Cell(52, 5, $asis->apellidos, 1, 0, 'L');
                      $pdf->Cell(42, 5, $asis->nombre, 1, 0, 'L');
                      $pdf->Cell(20, 5, $asis->fecha, 1, 0, 'L');
                      $pdf->Cell(70, 5, utf8_encode($aus[$asis->codigo]), 1, 1, 'L');
                      }
   }

$pdf->Output();