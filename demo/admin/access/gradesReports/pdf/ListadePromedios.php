<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Lista de promedios', 'List of averages'],
    ["Maestro(a)", "Teacher"],
    ["Grado:", "Grade:"],
    ["Curso", "Course"],
   ["Descripción", "Description"],
    ['Primer Semestre', 'First semester'],
    ['Segundo Semestre', 'Second semester'],
    ['D&#65533;as', 'Days'],
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
    }

}

function NLetra($valor){
  if($valor == ''){
    return '';
  }else if ($valor <= '100' && $valor >= '90') {
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

// $allGrades = $school->allGrades();
$pdf = new nPDF();
$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("Lista de promedios") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
$p = $_POST['pagina'];
$pro = $_POST['pro'];

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);
$pdf->AddPage('');
$a=0;
foreach ($students as $estu) {
    if ($a==$p)
       {
       $pdf->AddPage('');
       $a=0;
       }
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
//    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("Lista de promedios") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
   $pdf->SetFont('Arial', '', 12);

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(70, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(17, 5, '', 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Primer Semestre"), 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Segundo Semestre"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'Final', 1, 1, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("grado:"). " $estu->grado", 1, 0, 'L');
    $pdf->Cell(17, 5, $lang->translation("Curso"), 1, 0, 'C');

    $pdf->Cell(15, 5, $lang->translation("T-1"), 1, 0, 'C');
    $pdf->Cell(15, 5, $lang->translation("T-2"), 1, 0, 'C');
    $pdf->Cell(15, 5, $lang->translation("S-1"), 1, 0, 'C');
    $pdf->Cell(15, 5, $lang->translation("T-3"), 1, 0, 'C');
    $pdf->Cell(15, 5, $lang->translation("T-4"), 1, 0, 'C');
    $pdf->Cell(15, 5, $lang->translation("S-2"), 1, 0, 'C');
    $pdf->Cell(15, 5, $lang->translation("N-F"), 1, 1, 'C');

    $pdf->Cell(70, 5, $estu->nombre.' '.$estu->apellidos, 'LR', 0, 'L');
    $pdf->Cell(17, 5, '', 'LR', 0, 'C');
    $pdf->Cell(15, 5, '', 'LR', 0, 'C');
    $pdf->Cell(15, 5, '', 'LR', 0, 'C');
    $pdf->Cell(15, 5, '', 'LR', 0, 'C');
    $pdf->Cell(15, 5, '', 'LR', 0, 'C');
    $pdf->Cell(15, 5, '', 'LR', 0, 'C');
    $pdf->Cell(15, 5, '', 'LR', 0, 'C');
   $pdf->Cell(15, 5, '', 'LR', 1, 'C');
    $pdf->SetFillColor(89, 171, 227);
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
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
       if ($curso->nota1 > 0)
          {
          $cr[1] += 1;
          $nf[1] += $curso->nota1;
          }
       if ($curso->nota2 > 0)
          {
          $cr[2] += 1;
          $nf[2] += $curso->nota2;
          }
       if ($curso->sem1 > 0)
          {
          $cr[3] += 1;
          $nf[3] += $curso->sem1;
          }
       if ($curso->nota3 > 0)
          {
          $cr[4] += 1;
          $nf[4] += $curso->nota3;
          }
       if ($curso->nota4 > 0)
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
       
    $a1 = $a1 + 1 ;
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(7, 5, $a1, 'L', 0, 'R');
    $pdf->Cell(63, 5, $curso->descripcion, 'R', 0, 'L');
    $pdf->Cell(17, 5, $curso->curso, 'LR', 0, 'L');
    $pdf->Cell(15, 5, $curso->nota1, 'LR', 0, 'R');
    $pdf->Cell(15, 5, $curso->nota2, 'LR', 0, 'R');
    $pdf->Cell(15, 5, $curso->sem1, 'LR', 0, 'R');
    $pdf->Cell(15, 5, $curso->nota3, 'LR', 0, 'R');
    $pdf->Cell(15, 5, $curso->nota4, 'LR', 0, 'R');
    $pdf->Cell(15, 5, $curso->sem2, 'LR', 0, 'R');
    $pdf->Cell(15, 5, $curso->final, 'LR', 1, 'R');

        }


    $pdf->Cell(70, 5, 'Total', 'LRB', 0, 'R');
    $pdf->Cell(17, 5, '', 'LRB', 0, 'L');
    $pdf->Cell(15, 5, $cr[1] > 0 ? number_format($nf[1]/$cr[1],0) : '', 'LRB', 0, 'R');
    $pdf->Cell(15, 5, $cr[2] > 0 ? number_format($nf[2]/$cr[2],0) : '', 'LRB', 0, 'R');
    $pdf->Cell(15, 5, $cr[3] > 0 ? number_format($nf[3]/$cr[3],0) : '', 'LRB', 0, 'R');
    $pdf->Cell(15, 5, $cr[4] > 0 ? number_format($nf[4]/$cr[4],0) : '', 'LRB', 0, 'R');
    $pdf->Cell(15, 5, $cr[5] > 0 ? number_format($nf[5]/$cr[5],0) : '', 'LRB', 0, 'R');
    $pdf->Cell(15, 5, $cr[6] > 0 ? number_format($nf[6]/$cr[6],0) : '', 'LRB', 0, 'R');
    $pdf->Cell(15, 5, $cr[7] > 0 ? number_format($nf[7]/$cr[7],0) : '', 'LRB', 1, 'R');

       if ($p==2){$pdf->Ln(25);}
       if ($p==3){$pdf->Ln(15);}

   $a=$a+1;
   }

$pdf->Output();