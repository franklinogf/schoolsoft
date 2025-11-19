<?php
require_once __DIR__ . '/../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase0\DB;

Session::is_logged();

$lang = new Lang([
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    
]);


//session_start();
$grados =  $_COOKIE["variable1"];

$gg='09-12';
if ($grados=='A')
   {
   $pp=120;
   $gg='01-08';
   }


if ($grados=='A')
   {
   $gra8='01-';
   $gra7='02-';
   $gra6='03';
   $gra5='04';
   $gra4='05';
   $gra3='06';
   $gra2='07';
   $gra1='08';
   $gra0 = '08';
   }
if ($grados=='B')
   {
   $gra8='33';
   $gra7='33';
   $gra6='33';
   $gra5='33';
   $gra4='09';
   $gra3='10';
   $gra2='11';
   $gra1='12';
   $gra0 = '12';
}


if ($grados == 'B1') {
   $gra4 = '09';
   $gra3 = '10';
   $gra2 = '11';
   $gra1 = '12';
   $gra0 = '12';
}

if ($grados == 'C') {
   $gra4 = '09';
   $gra3 = '10';
   $gra2 = '11';
   $gra1 = '**';
   $gra0 = '11';
}

if ($grados == 'D') {
   $gra4 = '09';
   $gra3 = '10';
   $gra2 = '**';
   $gra1 = '**';
   $gra0 = '10';
   }
   
$mat[0] = "";
$mat[1] = "Español";
$mat[2] = "Ciencia";
$mat[3] = "Matematica";
$mat[4] = "Religión";
$mat[5] = "Inglés";
$mat[6] = "Estudios Sociales";
$mat[7] = "Educación Fisica";
$mat[8] = "Computadora";
$mat[9] = "Baile";

$cur[0] = "";
$cur[1] = "SPA";
$cur[2] = "BIO";
$cur[3] = "PCA";
$cur[4] = "ETI";
$cur[5] = "ENG";
$cur[6] = "PRH";
$cur[7] = "EF";
$cur[8] = "7777";
$cur[9] = "777";
$cur[10] = "SAL";
$cur[11] = "ESP";
$cur[12] = "FIS";
$cur[13] = "MAT";
$cur[14] = "EDC";
$cur[15] = "ING";
//$cur[16] = "ES0";
$cur[16] = "SOC";
$cur[17] = "EDF";
$cur[18] = "COM";
$cur[19] = "ART";
//$cur[20] = "777";

$cr[0] = 0;
$cr[1] = 1;
$cr[2] = 1;
$cr[3] = 1;
$cr[4] = 1;
$cr[5] = 1;
$cr[6] = 1;
$cr[7] = 0;
$cr[8] = 0;
$cr[9] = 0;
//$cr[10] = 0;

$school = new School(Session::id());
$year = $school->info('year2');

$db = new DB();
$db->query('Truncate acumula_totales');

$n7 = 0;
$n8 = 0;
$n9 = '';
$pdf = new PDF;

$pdf->SetTitle('INFORME PROMEDIO POR CLASE ACUMULADO');
$pdf->Fill();

    for ($i = 1; $i <= 9; $i++)
        {
        $cur1 = $cur[$i];
        $cur2 = $cur[10+$i];

        $students = DB::table('year')
   ->whereRaw("grado like '%" . $gra0 . "%' and year = '$year' and activo = ''")->orderBy('apellidos')->get();

      foreach ($students as $student)
              {

        $studa = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra1."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra1."%'")->orderBy('orden')->first();

        $studb = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra2."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra2."%'")->orderBy('orden')->first();

        $studc = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra3."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra3."%'")->orderBy('orden')->first();

        $studd = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra4."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra4."%'")->orderBy('orden')->first();

        $stude = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra5."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra5."%'")->orderBy('orden')->first();

        $studf = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra6."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra6."%'")->orderBy('orden')->first();

        $studg = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra7."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra7."%'")->orderBy('orden')->first();

        $studh = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and curso like '%".$cur1."%' and grado like '%".$gra8."%' or ss = '$student->ss' and curso like '%".$cur2."%' and grado like '%".$gra8."%'")->orderBy('orden')->first();

              $n1=0;$n2=0;$n3a=0;$n3b=0;$n3c=0;$n3d=0;$n3e=0;$n3f=0;$n3g=0;$n3h=0;$n4=0;$n5=0;$n6=0;
      if ($studa->sem1 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studa->sem1;
      }
      if ($studa->sem2 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studa->sem2;
      }
              if ($n1 > 0)
                 {
                 $n3a = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3a;
                 }
              $n1=0;$n2=0;
      if ($studb->sem1 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studb->sem1;
      }
      if ($studb->sem2 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studb->sem2;
      }
              if ($n1 > 0)
                 {
                 $n3b = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3b;
                 }
              $n1=0;$n2=0;
      if ($studc->sem1 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studc->sem1;
      }
      if ($studc->sem2 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studc->sem2;
      }
              if ($n1 > 0)
                 {
                 $n3c = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3c;
                 }
              $n1=0;$n2=0;
      if ($studd->sem1 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studd->sem1;
      }
      if ($studd->sem2 ?? 0 > 0) {
         $n1 = $n1 + 1;
         $n2 = $n2 + $studd->sem2;
      }
              if ($n1 > 0)
                 {
                 $n3d = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3d;
                 }
           $n3e = 0;$n3f = 0;$n3g = 0;$n3h = 0;
           if ($grados=='A')
              {
              $n1=0;$n2=0;
         if ($stude->sem1 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $stude->sem1;
         }
         if ($stude->sem2 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $stude->sem2;
         }
              if ($n1 > 0)
                 {
                 $n3e = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3e;
                 }
              $n1=0;$n2=0;
         if ($studf->sem1 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $studf->sem1;
         }
         if ($studf->sem2 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $studf->sem2;
         }
              if ($n1 > 0)
                 {
                 $n3f = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3f;
                 }
              $n1=0;$n2=0;
         if ($studg->sem1 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $studg->sem1;
         }
         if ($studg->sem2 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $studg->sem2;
         }
              if ($n1 > 0)
                 {
                 $n3g = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3g;
                 }
              $n1=0;$n2=0;
         if ($studh->sem1 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $studh->sem1;
         }
         if ($studh->sem2 ?? 0 > 0) {
            $n1 = $n1 + 1;
            $n2 = $n2 + $studh->sem2;
         }
              if ($n1 > 0)
                 {
                 $n3h = round($n2 / $n1,2);
                 $n4 = $n4 + 1;
                 $n5 = $n5 + $n3h;
                 }
              }

              if ($n4 > 0)
                 {
                 $n6 = round($n5 / $n4,2);

    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
        'curso' => $cur1,
        'not1' => $n3a,
        'not2' => $n3b,
        'not3' => $n3c,
        'not4' => $n3d,
        'not5' => $n3e,
        'not6' => $n3f,
        'not7' => $n3g,
        'not8' => $n3h,
        'final' => $n6,
    ]);




                 }
              }

        }


for ($e = 1; $e <= 9; $e++)       
    {
    $ce=0;
   $ce1 = 0;
    if ($grados=='A')
       {
       $pdf->AddPage('L');
       }
    else
       {
       $pdf->AddPage();
       }
    $pdf->SetFont('Times','',11);
    $cur2 = $cur[$e];
    $pdf->Cell(95,5,'Curso: '.utf8_encode($mat[$e]),1,0,'L',true);
    if ($grados=='A')
       {
       $pdf->Cell(15,5,$gra8,1,0,'C',true);
       $pdf->Cell(15,5,$gra7,1,0,'C',true);
       $pdf->Cell(15,5,$gra6,1,0,'C',true);
       $pdf->Cell(15,5,$gra5,1,0,'C',true);
       }
    $pdf->Cell(15,5,$gra4,1,0,'C',true);
    $pdf->Cell(15,5,$gra3,1,0,'C',true);
    $pdf->Cell(15,5,$gra2,1,0,'C',true);
    $pdf->Cell(15,5,$gra1,1,0,'C',true);
    $pdf->Cell(27,5,'PROMEDIOS',1,1,'C',true);
    $q = "select * from acumula_totales where curso like '%".$cur2."%' ORDER BY final DESC";
    $allGrades= DB::table('acumula_totales')
        ->whereRaw("curso like '%".$cur2."%'")->orderBy('final DESC')->get();

  foreach ($allGrades as $grade)
          {
      if ($grados == 'A' and $ce1 > 26) {
         $ce1 = 0;
         $pdf->AddPage('L');
      }
          
          $ce=$ce+1;
      $ce1 = $ce1 + 1;
          $pdf->Cell(10,5,$ce,1,0,'R');
          $pdf->Cell(85,5,$grade->apellidos.' '.$grade->nombre,1,0,'L');
          if ($grados=='A')
             {
         $pdf->Cell(15, 5, number_format($grade->not8, 2), 1, 0, 'R');
         $pdf->Cell(15, 5, number_format($grade->not7, 2), 1, 0, 'R');
         $pdf->Cell(15, 5, number_format($grade->not6, 2), 1, 0, 'R');
         $pdf->Cell(15, 5, number_format($grade->not5, 2), 1, 0, 'R');
             }
      $pdf->Cell(15, 5, number_format($grade->not4, 2), 1, 0, 'R');
      $pdf->Cell(15, 5, number_format($grade->not3, 2), 1, 0, 'R');
      $pdf->Cell(15, 5, number_format($grade->not2, 2), 1, 0, 'R');
      $pdf->Cell(15, 5, number_format($grade->not1, 2), 1, 0, 'R');
          $pdf->Cell(27,5,number_format($grade->final,2),1,1,'R');

          }

    }


$pdf->Output();


