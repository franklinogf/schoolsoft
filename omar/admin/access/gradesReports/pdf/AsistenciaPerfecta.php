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
    ['Asistencia perfecta/tardanzaa', 'Perfect assistance/Delays'],
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
    ['S.S.', 'S.S.'],
    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();
$tda = $_POST['asis'];
$grado = $_POST['grade'];
$year = $school->info('year2');
$fa = [];
$fa[1] = $school->info('asis1');
$fa[2] = $school->info('asis2');
$fa[3] = $school->info('asis3');
$fa[4] = $school->info('asis4');
$fa[5] = $school->info('asis5');
$fa[6] = $school->info('asis6');
$fa[7] = $school->info('asis7');
$fa[8] = $school->info('asis8');

if ($grado == 'todos')
   {
   $allGrades = $school->allGrades();
   }
else
   {
   $allGrades = [];
   $allGrades[1] = $grado;
   }
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Asistencia perfecta/tardanzaa") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Asistencia perfecta/tardanzaa") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("T-1"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("T-2"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("T-3"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("T-4"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        list($ss1, $ss2, $ss3) = explode("-", $student->ss);
        if ($tda == 1)
           {
           $asistencia = DB::table('asispp')->where([
             ['year', $year],
             ['grado', $grade],
             ['ss', $student->ss],
             ])->orderBy('apellidos')->get();
         $aus = 0;
           $tar = [];
         $aus = array(5);
         $tar = array(5);
         $aus[1] = 0;
         $aus[2] = 0;
         $aus[3] = 0;
         $aus[4] = 0;
         $aus[5] = 0;
         $tar[1] = 0;
         $tar[2] = 0;
         $tar[3] = 0;
         $tar[4] = 0;
         $tar[5] = 0;
           foreach ($asistencia as $asis) 
                   {
                   if ($asis->fecha >= $fa[1] and $asis->fecha <= $fa[2] and $asis->codigo < 8)
                      {
                      $aus[1] += 1;
                      }
                   if ($asis->fecha >= $fa[1] and $asis->fecha <= $fa[2] and $asis->codigo > 7)
                      {
                      $tar[1] += 1;
                      }
                   if ($asis->fecha >= $fa[3] and $asis->fecha <= $fa[4] and $asis->codigo < 8)
                      {
                      $aus[2] += 1;
                      }
                   if ($asis->fecha >= $fa[3] and $asis->fecha <= $fa[4] and $asis->codigo > 7)
                      {
                      $tar[2] += 1;
                      }

                   if ($asis->fecha >= $fa[5] and $asis->fecha <= $fa[6] and $asis->codigo < 8)
                      {
                      $aus[3] += 1;
                      }
                   if ($asis->fecha >= $fa[5] and $asis->fecha <= $fa[6] and $asis->codigo > 7)
                      {
                      $tar[3] += 1;
                      }
                   if ($asis->fecha >= $fa[7] and $asis->fecha <= $fa[8] and $asis->codigo < 8)
                      {
                      $aus[4] += 1;
                      }
                   if ($asis->fecha >= $fa[7] and $asis->fecha <= $fa[8] and $asis->codigo > 7)
                      {
                      $tar[4] += 1;
                      }
                   }
           $aus[5] += $aus[1] + $aus[2] + $aus[3] + $aus[4];
           }

        if ($tda == 2)
           {
           $asistencia = DB::table('padres')->where([
             ['year', $year],
             ['grado', $grade],
             ['ss', $student->ss],
            ])->orderBy('apellidos')->get();
           $aus = [];
           $tar = [];
           foreach ($asistencia as $asis) {
                   $aus[1] += $asis->aus1;
                   $aus[2] += $asis->aus2;
                   $aus[3] += $asis->aus3;
                   $aus[4] += $asis->aus4;
                   $aus[5] += $asis->aus1 + $asis->aus2 + $asis->aus3 + $asis->aus4;
                   $tar[1] += $asis->tar1;
                   $tar[2] += $asis->tar2;
                   $tar[3] += $asis->tar3;
                   $tar[4] += $asis->tar4;
                   }
           }
        if ($aus[5] == 0)
           {
           $gender = Util::gender($student->genero);
           $genderCount[$gender]++;
           $genderCount['T']++;
           $pdf->Cell(10, 5, $count + 1, 1, 0, 'R');
           $pdf->Cell(20, 5, $student->id, 1, 0, 'C');
           $pdf->Cell(55, 5, $student->apellidos, 1);
           $pdf->Cell(45, 5, $student->nombre, 1, 0);
           $pdf->Cell(15, 5, $aus[1].' / '.$tar[1], 1, 0, 'C');
           $pdf->Cell(15, 5, $aus[2].' / '.$tar[2], 1, 0, 'C');
           $pdf->Cell(15, 5, $aus[3].' / '.$tar[3], 1, 0, 'C');
           $pdf->Cell(15, 5, $aus[4].' / '.$tar[4], 1, 1, 'C');
           }
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
