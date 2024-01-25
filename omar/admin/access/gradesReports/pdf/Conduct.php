<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Conducta por grado', 'Conduct by grade'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
    ['Trimestre', 'Quarter'],
    ['Promedio', 'Average'],
    ['Nota B', 'Note B'],
    ['Nota C', 'Note C'],
    ['Nota D', 'Note D'],
    ['Nota F', 'Note F'],
    ['Otros', 'Other'],
    ['Total', 'Total'],
    ['P-C', 'QZ'],
    ['TPA', 'TAP'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Trabajos Diarios', 'Daily Homework'],
    ['Trabajos Libreta', 'Homework'],
    ['Fecha', 'Date'],
    ['Tema', 'Topic'],
    ['Valor', 'Value'],
    ['Pruebas Cortas', 'Quiz'],
    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],
    ['Sem-1', 'Sem-1'],
    ['Sem-2', 'Sem-2'],
    ['Final', 'Final'],

]);

$pdf = new PDF();

$school = new School(Session::id());
$grado = $_POST['grade'];
$nota = $_POST['nota'];
$divicion= $_POST['divicion'];
list($nota,$tt) = explode("-",$_POST['nota']);

$cl = $_POST['cl'];
$notar = $_POST['notar'];


//$year = $school->year();
$year = $school->info('year2');
//$pdf = new nPDF();
$pdf = new PDF();





if ($grado == 'all')
   {
   $allGrades = $school->allGrades();
   }
else
   {
   $allGrades = DB::table('year')->select("distinct grado")->where([
          ['year', $year],
          ['grado', $grado],
        ])->orderBy('grado')->first();
   }

foreach ($allGrades as $grade) 
        {
        $pdf->AddPage('');
        $pdf->Cell(0, 5, $lang->translation("Conducta por grado").' / '.$grade." / $tt / $year", 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Fill();
        $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $pdf->Cell(15, 5, $lang->translation("T-1"), 1, 0, 'C', true);
        $pdf->Cell(15, 5, $lang->translation("T-2"), 1, 0, 'C', true);
        $pdf->Cell(15, 5, $lang->translation("T-3"), 1, 0, 'C', true);
        $pdf->Cell(15, 5, $lang->translation("T-4"), 1, 1, 'C', true);
        $n = 0;
        $materias = [];
        $curs = [];
        $estudiantes = [];
        $c = 0;
//**********************


         $students = DB::table('year')->where([
                  ['year', $year],
                  ['grado', $grade],
                  ])->orderBy('fin','desc')->get();
        $t=0;
        foreach ($students as $student) 
                {
                $t=$t+1;
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 5, $t, 1, 0, 'R');
                $pdf->Cell(55, 5, $student->apellidos, 1, 0, 'L');
                $pdf->Cell(45, 5, $student->nombre, 1, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $a=0;$b=0;
                $pdf->Cell(15, 5, '', 1, 0, 'R');
                $pdf->Cell(15, 5, '', 1, 0, 'R');
                $pdf->Cell(15, 5, '', 1, 0, 'R');
                $pdf->Cell(15, 5, '', 1, 1, 'R');
                }



//**********************
         }


$pdf->Output();