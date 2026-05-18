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
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Descripci&#65533;n", "Description"],
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
               $a1=0;$t1=0;
               $a2=0;$t2=0;
               $a3=0;$t3=0;
               $a4=0;$t4=0;
               $a5=0;$t5=0;
               $a6=0;$t6=0;
               $a7=0;$t7=0;
               foreach ($cursos as $curso) 
                       {

                       if ($suma == 'N' and $curso->nota1 > 0)
                          {
                          $a1=$a1+$curso->nota1;$t1=$t1+1;
                          }
                       if ($suma == 'N' and $curso->nota2 > 0)
                          {
                          $a2=$a2+$curso->nota2;$t2=$t2+1;
                          }
                       if ($suma == 'N' and $curso->nota3 > 0)
                          {
                          $a3=$a3+$curso->nota3;$t3=$t3+1;
                          }
                       if ($suma == 'N' and $curso->nota4 > 0)
                          {
                          $a4=$a4+$curso->nota4;$t4=$t4+1;
                          }

                       if ($suma == 'N' and $curso->final > 0)
                          {
                          $a7=$a7+$curso->final;$t7=$t7+1;
                          }
                       if ($suma == 'N' and $curso->sem1 > 0)
                          {
                          $a5=$a5+$curso->sem1;$t5=$t5+1;
                          }
                       if ($suma == 'N' and $curso->sem2 > 0)
                          {
                          $a6=$a6+$curso->sem2;$t6=$t6+1;
                          }

                       if ($suma == 'C' and $curso->nota1 > 0 and $curso->credito > 0)
                          {
                          $a1=$a1+round($curso->nota1*$curso->credito,0);$t1=$t1+$curso->credito;
                          }
                       if ($suma == 'C' and $curso->nota2 > 0 and $curso->credito > 0)
                          {
                          $a2=$a2+round($curso->nota2*$curso->credito,0);$t2=$t2+$curso->credito;
                          }
                       if ($suma == 'C' and $curso->nota3 > 0 and $curso->credito > 0)
                          {
                          $a3=$a3+round($curso->nota3*$curso->credito,0);$t3=$t3+$curso->credito;
                          }
                       if ($suma == 'C' and $curso->nota4 > 0 and $curso->credito > 0)
                          {
                          $a4=$a4+round($curso->nota4*$curso->credito,0);$t4=$t4+$curso->credito;
                          }



                       if ($suma == 'C' and $curso->final > 0 and $curso->credito > 0)
                          {
                          $a7=$a7+round($curso->final*$curso->credito,0);$t7=$t7+$curso->credito;
                          }
                       if ($suma == 'C' and $curso->sem1 > 0 and $curso->credito > 0)
                          {
                          $a5=$a5+round($curso->sem1*$curso->credito,0);$t5=$t5+$curso->credito;
                          }
                       if ($suma == 'C' and $curso->sem2 > 0 and $curso->credito > 0)
                          {
                          $a6=$a6+round($curso->sem2*$curso->credito,0);$t6=$t6+$curso->credito;
                          }
                       }
               if ($t7 > 0)
                  {
                  $b7=round($a7/$t7,$r);
                  $b1=0;
                  $b2=0;
                  $b3=0;
                  $b4=0;
                  $b5=0;
                  $b6=0;
               if ($t1 > 0){$b1=round($a1/$t1,$r);}
               if ($t2 > 0){$b2=round($a2/$t2,$r);}
               if ($t3 > 0){$b3=round($a3/$t3,$r);}
               if ($t4 > 0){$b4=round($a4/$t4,$r);}
               if ($t5 > 0){$b5=round($a5/$t5,$r);}
               if ($t6 > 0){$b6=round($a6/$t6,$r);}
                  $updates = [
                             'tr1' => $b1,
                             'tr2' => $b2,
                             'tr3' => $b3,
                             'tr4' => $b4,
                             'fin' => $b7,
                             'se1' => $b5,
                             'se2' => $b6,];
                  DB::table('year')->where('mt', $studen->mt)->update($updates);
                  }
               }
               
        }


//$pdf = new nPDF();
$pdf = new PDF();
$cur = $_POST['curso'] ?? '';
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
      $pdf->SetFont('Arial', '', 11);
      $pdf->Fill();
      $pdf->Cell(7, 5, '', 1, 0, 'C', true);
      $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
      $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
      $pdf->Cell(12, 5, 'T-1', 1, 0, 'C', true);
      $pdf->Cell(12, 5, 'T-2', 1, 0, 'C', true);
      $pdf->Cell(12, 5, 'S-1', 1, 0, 'C', true);
      $pdf->Cell(12, 5, 'S-2', 1, 0, 'C', true);
      $pdf->Cell(12, 5, 'T-3', 1, 0, 'C', true);
      $pdf->Cell(12, 5, 'T-4', 1, 0, 'C', true);
      $pdf->Cell(12, 5, 'Final', 1, 1, 'C', true);
      $pdf->SetFont('Arial', '', 10);
      $n = 0;
      foreach ($students as $estu) 
              {
              $n = $n +1;
              $pdf->Cell(7, 5, $n, 1, 0, 'R');
              $pdf->Cell(55, 5, $estu->apellidos, $cl, 0, 'L');
              $pdf->Cell(45, 5, $estu->nombre, $cl, 0, 'L');
              $pdf->Cell(12, 5, number_format($estu->tr1,$r), $cl, 0, 'R');
              $pdf->Cell(12, 5, number_format($estu->tr2,$r), $cl, 0, 'R');
              $pdf->Cell(12, 5, number_format($estu->se1,$r), $cl, 0, 'R');
              $pdf->Cell(12, 5, number_format($estu->tr3,$r), $cl, 0, 'R');
              $pdf->Cell(12, 5, number_format($estu->tr4,$r), $cl, 0, 'R');
              $pdf->Cell(12, 5, number_format($estu->se2,$r), $cl, 0, 'R');
              $pdf->Cell(12, 5, number_format($estu->fin,$r), $cl, 1, 'R');
              }
      }


$pdf->Output();