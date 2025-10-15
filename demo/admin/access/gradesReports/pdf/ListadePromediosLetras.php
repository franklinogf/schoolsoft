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
    ['Lista de promedios Decimales', 'List of averages'],
    ["Maestro(a)", "Teacher"],
    ["Grado:", "Grade:"],
    ["Curso", "Course"],
   ["Descripción", "Description"],
    ['Primer Semestre', 'First semester'],
    ['Segundo Semestre', 'Second semester'],
    ['Días', 'Days'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha:', 'Date:'],
    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],
    ['S-1', 'S-1'],
    ['S-2', 'S-2'],
    ['N-F', 'F-N'],
]);

class nPDF extends PDF
{
    function header()
    {
       global $year;
       parent::header();
   
    }

}

function NLetra($valor){
  if($valor == ''){
    return '';
  }else if ($valor >= '90') {
    return 'A';
  }else if ($valor <= '89' && $valor >= '80') {
    return 'B';
  }else if ($valor <= '79' && $valor >= '70') {
    return 'C';
  }else if ($valor <= '69' && $valor >= '60') {
    return 'D';
  }else  if ($valor <= '59') {
    return 'F';
  }
}


$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year2');
$grade1 = $_POST['grade'];
$pro = $_POST['pro'];

$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->Fill();
if ($grade1 == 'todos')
   {
   $allGrades = DB::table('year')->select("DISTINCT grado")->where([
        ['year', $year]
    ])->orderBy('grado')->get();
   }
else
   {
   $allGrades = DB::table('year')->select("DISTINCT grado")->where([
        ['grado', $grade1],
        ['year', $year]
    ])->orderBy('grado')->get();
   }




    foreach ($allGrades as $grade){


$pdf->SetTitle($lang->translation("Lista de promedios Decimales") . " $year", true);
$teacher = $teacherClass->findByGrade($grade->grado);
$students = $studentClass->findByGrade($grade->grado);
    $pdf->AddPage('L');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Lista de promedios Decimales")." Grado: $grade->grado /" . utf8_encode(" Año: $year"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(90, 5, $lang->translation("Nombre"), 1, 0, 'C', true);

    $pdf->Cell(25, 5, $lang->translation("T-1"), 1, 0, 'C', true);
    $pdf->Cell(25, 5, $lang->translation("T-2"), 1, 0, 'C', true);
    $pdf->Cell(25, 5, $lang->translation("S-1"), 1, 0, 'C', true);
    $pdf->Cell(25, 5, $lang->translation("T-3"), 1, 0, 'C', true);
    $pdf->Cell(25, 5, $lang->translation("T-4"), 1, 0, 'C', true);
    $pdf->Cell(25, 5, $lang->translation("S-2"), 1, 0, 'C', true);
    $pdf->Cell(25, 5, $lang->translation("N-F"), 1, 1, 'C', true);


$a=0;
foreach ($students as $estu) {
    $pdf->SetFont('Arial', '', 10);
       $a=$a+1;

    $materias = [];
    $cursos = [];
    $estudiantes = [];

    $pdf->Cell(10, 5, $a, 'LR', 0, 'R');
    $pdf->Cell(80, 5, $estu->nombre.' '.$estu->apellidos, 'LR', 0, 'L');
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade->grado],
          ['curso', '!=', ''],
          ['curso', 'NOT LIKE', '%AA-%']
        ])->orderBy('orden DESC')->get();
    $crs = 0;    
    $a1 = 0;
   $nf = [];
   $nf[1] = 0;
   $nf[2] = 0;
   $nf[3] = 0;
   $nf[4] = 0;
   $nf[5] = 0;
   $nf[6] = 0;
   $nf[7] = 0;
   $cr = [];
   $cr[1] = 0;
   $cr[2] = 0;
   $cr[3] = 0;
   $cr[4] = 0;
   $cr[5] = 0;
   $cr[6] = 0;
   $cr[7] = 0;
    foreach ($cursos as $curso) {
    if ($pro==1 and $curso->credito > 0)
       {
       if ($curso->nota1 > 0)
          {
          $cr[1] += $curso->credito;
          $nf[1] += round($curso->nota1 * $curso->credito);
          }
       if ($curso->nota2 > 0)
          {
          $cr[2] += $curso->credito;
          $nf[2] += round($curso->nota2 * $curso->credito);
          }
       if ($curso->sem1 > 0)
          {
          $cr[3] += $curso->credito;
          $nf[3] += round($curso->sem1 * $curso->credito);
          }
       if ($curso->nota3 > 0)
          {
          $cr[4] += $curso->credito;
          $nf[4] += round($curso->nota3 * $curso->credito);
          }
       if ($curso->nota4 > 0)
          {
          $cr[5] += $curso->credito;
          $nf[5] += round($curso->nota4 * $curso->credito);
          }
       if ($curso->sem2 > 0)
          {
          $cr[6] += $curso->credito;
          $nf[6] += round($curso->sem2 * $curso->credito);
          }
       if ($curso->final > 0)
          {
          $cr[7] += $curso->credito;
          $nf[7] += round($curso->final * $curso->credito);
          }
       }
    if ($pro==2)
       {
       if ($curso->nota1 > 0 and $curso->nota1 < 200)
          {
          $cr[1] += 1;
          $nf[1] += $curso->nota1;
          }
       if ($curso->nota2 > 0 and $curso->nota2 < 200)
          {
          $cr[2] += 1;
          $nf[2] += $curso->nota2;
          }
       if ($curso->sem1 > 0)
          {
          $cr[3] += 1;
          $nf[3] += $curso->sem1;
          }
       if ($curso->nota3 > 0 and $curso->nota3 < 200)
          {
          $cr[4] += 1;
          $nf[4] += $curso->nota3;
          }
       if ($curso->nota4 > 0 and $curso->nota4 < 200)
          {
          $cr[5] += 1;
          $nf[5] += $curso->nota4;
          }
       if ($curso->sem2 > 0)
          {
          $cr[6] += 1;
          $nf[6] += $curso->sem2;
          }
       if ($curso->final > 0)
          {
          $cr[7] += 1;
          $nf[7] += $curso->final;
          }
       }
    if ($pro==3 and $curso->peso > 0)
       {
       if ($curso->nota1 > 0)
          {
          $cr[1] += $curso->peso ;
          $nf[1] += round($curso->nota1 * $curso->peso);
          }
       if ($curso->nota2 > 0)
          {
          $cr[2] += $curso->peso;
          $nf[2] += round($curso->nota2 * $curso->peso);
          }
       if ($curso->sem1 > 0)
          {
          $cr[3] += $curso->peso;
          $nf[3] += round($curso->sem1 * $curso->peso);
          }
       if ($curso->nota3 > 0)
          {
          $cr[4] += $curso->peso;
          $nf[4] += round($curso->nota3 * $curso->peso);
          }
       if ($curso->nota4 > 0)
          {
          $cr[5] += $curso->peso;
          $nf[5] += round($curso->nota4 * $curso->peso);
          }
       if ($curso->sem2 > 0)
          {
          $cr[6] += $curso->peso;
          $nf[6] += round($curso->sem2 * $curso->peso);
          }
       if ($curso->final > 0)
          {
          $cr[7] += $curso->peso;
          $nf[7] += round($curso->final * $curso->peso);
          }
      }
       
    $pdf->SetFont('Arial', '', 10);

    }

for ($x = 1; $x <= 7; $x++) {
    if ($cr[$x] > 0 and $nf[$x] > 0)
       {
       $va = number_format($nf[$x]/$cr[$x],0);
       if ($va > 100){$va=100;}
       $decimal = DB::table('tablas')->where([
           ['valor', $va]
           ])->first();
       $va = number_format($nf[$x]/$cr[$x],0);
       $pdf->Cell(9, 5, $va, 'LRB', 0, 'R');
       $pdf->Cell(9, 5, $va > 0 ? number_format($decimal->punto ?? 0,2) : '', 'LRB', 0, 'R');
       $pdf->Cell(7, 5, NLetra($va), 'LRB', 0, 'C');
       }
    else
       {
       $pdf->Cell(9, 5, '', 'LRB', 0, 'R');
       $pdf->Cell(9, 5, '', 'LRB', 0, 'R');
       $pdf->Cell(7, 5, '', 'LRB', 0, 'R');
       }
    }
    $pdf->Cell(1, 5, '', 0, 1, 'R');

   }
   }

$pdf->Output();