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
    ['Lista de rango', 'Rank List'],
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
        $pdf->AddPage('L');
        $pdf->Cell(0, 5, $lang->translation("Lista de rango").' / '.$grade." / $tt / $year", 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Fill();
        $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $cursos = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
            ['year', $year],
            ['grado', $grade],
            ])->orderBy('orden')->get();
        $n = 0;
        $materias = [];
        $curs = [];
        $estudiantes = [];
        $c = 0;
        foreach ($cursos as $curso) 
                {
                $pdf->Cell(12, 5, substr($curso->curso, 0, 3), 1, 0, 'C', true);
                $curs[] = $curso->curso;
                $c = $c + 1;
                }
                $pdf->Cell(12, 5, $lang->translation("Final"), 1, 1, 'C', true);

            $students = DB::table('year')->where([
                   ['year', $year],
                   ['grado', $grade],
                   ])->orderBy('apellidos')->get();
        foreach ($students as $student) 
                {
                $a=0;$b=0;
                for ($i = 0; $i < $c; $i++) 
                    {
                    $stu = DB::table('padres')->where([
                       ['year', $year],
                       ['ss', $student->ss],
                       ['grado', $grade],
                       ['curso', $curs[$i]],
                       ])->orderBy('curso')->first();
                    if ($divicion == 'N' and $stu->$nota > 0)
                       {
                       $a=$a+$stu->$nota;$b=$b+1;
                       }
                    if ($divicion == 'C' and $stu->$nota > 0 and $stu->credito > 0)
                       {
                       $a=$a+round($stu->$nota*$stu->credito,0);$b=$b+$stu->credito;
                       }
                    }
                  if ($b > 0)
                     {
                     $n = round($a / $b,2);
                     $updates = ['fin' => $n,];
                     DB::table('year')->where('mt', $student->mt)->update($updates);
                     }

            }
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
                $pdf->Cell(10, 4, $t, $cl, 0, 'R');
                $pdf->Cell(55, 4, $student->apellidos, $cl, 0, 'L');
                $pdf->Cell(45, 4, $student->nombre, $cl, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $a=0;$b=0;
                for ($i = 0; $i < $c; $i++) 
                    {
                    $stu = DB::table('padres')->where([
                       ['year', $year],
                       ['ss', $student->ss],
                       ['grado', $grade],
                       ['curso', $curs[$i]],
                       ])->orderBy('curso')->first();
                    $pdf->Cell(12, 4, $stu->$nota, $cl, 0, 'C');
                    }
                    $pdf->Cell(12, 4, number_format($student->fin,$notar), $cl, 1, 'R');
                }



//**********************
         }


$pdf->Output();