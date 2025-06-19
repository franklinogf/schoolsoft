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
    ['Lista de promedios', 'List of averages'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Descripción", "Description"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Trimestre', 'Quarter'],
    ['Nota A', 'Note A'],
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
    ['Final', 'Final'],

]);

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grupo;
        parent::header();
        $ct = $_POST['nota'];
        if ($ct=='se1'){$tt=$lang->translation("S-1");}
        if ($ct=='se2'){$tt=$lang->translation("S-2");}
        if ($ct=='fin'){$tt=$lang->translation("Final");}
        $this->SetFont('Arial', 'B', 12);

        $this->Cell(0, 5, $lang->translation("Lista de promedios")." / $year", 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
    $this->Fill();
    $this->Cell(10, 5, '', 1, 0, 'C', true);
    $this->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $this->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $this->Cell(25, 5, 'S-1', 1, 0, 'C', true);
    $this->Cell(25, 5, 'S-2', 1, 0, 'C', true);
    $this->Cell(25, 5, 'Final', 1, 1, 'C', true);
    $this->SetFont('Arial', '', 11);


    }
}


$school = new School(Session::id());

$year = $school->info('year2');
$grado = $_POST['grade'];
$ct = $_POST['nota'];
$suma = $_POST['divicion'];
$r = $_POST['notar'];

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


foreach ($allGrades as $grade) {
        $studens = DB::table('year')->where([
          ['year', $year],
          ['grado', $grade],
        ])->orderBy('apellidos')->get();
        foreach ($studens as $studen) 
               {
               $cursos = DB::table('padres')->where([
                 ['ss', $studen->ss],
                 ['year', $year],
                 ['grado', $grade],
                 ['curso', '!=', ''],
                 ['curso', 'NOT LIKE', '%AA-%']
               ])->orderBy('orden')->get();
               $a=0;$t=0;
               $a2=0;$t2=0;
               $a3=0;$t3=0;
               foreach ($cursos as $curso) 
                       {
                       if ($suma == 'N' and $curso->final > 0)
                          {
                          $a=$a+$curso->final;$t=$t+1;
                          }
                       if ($suma == 'N' and $curso->sem1 > 0)
                          {
                          $a2=$a2+$curso->sem1;$t2=$t2+1;
                          }
                       if ($suma == 'N' and $curso->sem2 > 0)
                          {
                          $a3=$a3+$curso->sem2;$t3=$t3+1;
                          }
                       if ($suma == 'C' and $curso->final > 0 and $curso->credito > 0)
                          {
                          $a=$a+round($curso->final*$curso->credito,0);$t=$t+$curso->credito;
                          }
                       if ($suma == 'C' and $curso->sem1 > 0 and $curso->credito > 0)
                          {
                          $a2=$a2+round($curso->sem1*$curso->credito,0);$t2=$t2+$curso->credito;
                          }
                       if ($suma == 'C' and $curso->sem2 > 0 and $curso->credito > 0)
                          {
                          $a3=$a3+round($curso->sem2*$curso->credito,0);$t3=$t3+$curso->credito;
                          }
                       }
               if ($t > 0)
                  {
                  $b=round($a/$t,$r);
                  $b2=0;
                  $b3=0;
               if ($t2 > 0){$b2=round($a2/$t2,$r);}
               if ($t3 > 0){$b3=round($a3/$t3,$r);}
                  $updates = ['fin' => $b,
                             'se1' => $b2,
                             'se2' => $b3,];
                  DB::table('year')->where('mt', $studen->mt)->update($updates);
                  }
               }
               
        }


//$pdf = new nPDF();
$pdf = new PDF();
$cur = $_POST['curso'];
$cl = $_POST['cl'];
$x = 0;
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
    $students = DB::table('year')->where([
          ['year', $year],
          ['grado', $grade],
          ['codigobaja', ''],
        ])->orderBy($ct,'desc')->get();
      if ($ct=='se1'){$tt="S-1";}
      if ($ct=='se2'){$tt="S-2";}
      if ($ct=='fin'){$tt="Final";}

      $pdf->AddPage('');
      $pdf->SetFont('Arial', 'B', 12);
      $pdf->Cell(0, 5, $lang->translation("Lista de promedios").' / '.$grade." / $tt / $year", 0, 1, 'C');
      $pdf->Ln(5);
      $pdf->SetFont('Arial', '', 12);
      $pdf->Fill();
      $pdf->Cell(10, 5, '', 1, 0, 'C', true);
      $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
      $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
      $pdf->Cell(25, 5, 'S-1', 1, 0, 'C', true);
      $pdf->Cell(25, 5, 'S-2', 1, 0, 'C', true);
      $pdf->Cell(25, 5, 'Final', 1, 1, 'C', true);
      $pdf->SetFont('Arial', '', 11);
      $n = 0;
      foreach ($students as $estu) 
              {
              $n = $n +1;
              $pdf->Cell(10, 5, $n, 1, 0, 'R');
              $pdf->Cell(55, 5, $estu->apellidos, $cl, 0, 'L');
              $pdf->Cell(45, 5, $estu->nombre, $cl, 0, 'L');
              $pdf->Cell(25, 5, number_format($estu->se1,$r), $cl, 0, 'R');
              $pdf->Cell(25, 5, number_format($estu->se2,$r), $cl, 0, 'R');
              $pdf->Cell(25, 5, number_format($estu->fin,$r), $cl, 1, 'R');
              }
      }


$pdf->Output();