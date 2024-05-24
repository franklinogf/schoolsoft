<?php
require_once '../../../app.php';

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


$school = new School(Session::id());
$year = $school->info('year2');

session_start();
$grados =  $_COOKIE["variable1"];
$pdf = new PDF;
$pdf->SetTitle('INFORME PROMEDIO POR CLASE ACUMULADO');
$pdf->Fill();

$pdf->AliasNbPages();
if ($grados=='B')
   {
   $pdf->AddPage();
   }
else
   {
   $pdf->AddPage('L');
   }

$pdf->Cell(0,5,'RANGO / '.$year,0,1,'C');
$pdf->Cell(0,5,'',0,1,'L');

$pdf->SetFont('Times','B',11);
//$pdf->SetFillColor(240);

$pdf->Cell(15,5,'',1,0,'L',true);
$pdf->Cell(25,5,'RANGO',1,0,'C',true);
$pdf->Cell(90,5,'NOMBRE DEL ESTUDIANTE',1,0,'C',true);
if ($grados == 'A')
   {
   $pdf->Cell(12,5,'01',1,0,'C',true);
   $pdf->Cell(12,5,'02',1,0,'C',true);
   $pdf->Cell(12,5,'03',1,0,'C',true);
   $pdf->Cell(12,5,'04',1,0,'C',true);
   $pdf->Cell(12,5,'05',1,0,'C',true);
   $pdf->Cell(12,5,'06',1,0,'C',true);
   $pdf->Cell(12,5,'07',1,0,'C',true);
   $pdf->Cell(12,5,'08',1,0,'C',true);
   } else {
   $pdf->Cell(12, 5, '09', 1, 0, 'C', true);
   $pdf->Cell(12, 5, '10', 1, 0, 'C', true);
   $pdf->Cell(12, 5, '11', 1, 0, 'C', true);
   $pdf->Cell(12, 5, '12', 1, 0, 'C', true);
}

$pdf->Cell(12,5,'PRO',1,1,'C',true);
$pdf->SetFont('Times','',12);

$db = new DB();
$db->query('Truncate acumula_totales');

IF ($grados=='A')
   {
   $gra1='08';
   $gra2='07';
   $gra3='06';
   $gra4='05';
   $gra5='04';
   $gra6='03-';
   $gra7='02-';
   $gra8='01-';
   $gra0 = '08';
   }
IF ($grados=='B')
   {
   $gra4='09';
   $gra3='10';
   $gra2='11';
   $gra1='12';
   $gra0 = '12';
   }

if ($grados == 'C') {
   $gra4 = '09';
   $gra3 = '10';
   $gra2 = '11';
   $gra1 = '22';
   $gra0 = '11';
}

if ($grados == 'D') {
   $gra4 = '09';
   $gra3 = '10';
   $gra2 = '22';
   $gra1 = '22';
   $gra0 = '10';
}



$a=0;
$nt1=0;
$nt2=0;
$nt3=0;
$nt4=0;
$nt5=0;
$nt6=0;
$cr1=0;
$cr2=0;
$cr3=0;
$cep1=0;
$cep2=0;
$cep3=0;
$nta1=0;
$nta2=0;
$nta3=0;
$ng=0;
$notF=0;
$notF1=0;

$students = DB::table('year')
->whereRaw("grado like '%" . $gra0 . "%' and year = '$year' and activo = ''")->orderBy('apellidos')->get();

//$q7 = "select * from year where activo='' and grado like '%".$gra1."%' AND year = '$_POST[year2]' ORDER BY apellidos ASC";
//$result7=mysql_query($q7);
foreach ($students as $student)
        {
        $ng=0;
        $cr2=0;
        $cra1=0;
        $cra2=0;
        $cra3=0;
        $cra4=0;
        $cra5=0;
        $cep1=0;
        $nta1=0;
        $nta2=0;
        $nta3=0;
        $cr2=0;
        $ng=0;
        $notF =0;
        $let2=0;
        $cnt2=0;

        $studa = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra1."%'")->orderBy('orden')->get();

        $studb = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra2."%'")->orderBy('orden')->get();

        $studc = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra3."%'")->orderBy('orden')->get();

        $studd = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra4."%'")->orderBy('orden')->get();

        $stude = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra5."%'")->orderBy('orden')->get();
        $studf = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra6."%'")->orderBy('orden')->get();
        $studg = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra7."%'")->orderBy('orden')->get();
        $studh = DB::table('acumulativa')
        ->whereRaw("ss = '$student->ss' and grado like '%".$gra8."%'")->orderBy('orden')->get();


      $let1=0;
      $let3=0;
      $cnt1=0;
      $cnt3=0;
    foreach ($studa as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);


      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
    $thisCourse2 = DB::table('acumula_totales')->where([
        ['ss', $student->ss]
    ])->update([
        'not1' => number_format($lets1/$lets2,2),
    ]);

         }
      $let1=0;
      $let3=0;
      $cnt1=0;
      $cnt3=0;
    foreach ($studb as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);
      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
       $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
       ])->update([
           'not2' => number_format($lets1/$lets2,2),
       ]);

         }

      $let1=0;
      $let3=0;
      $cnt1=0;
      $cnt3=0;
    foreach ($studc as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);
      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
         $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
        ])->update([
           'not3' => number_format($lets1/$lets2,2),
        ]);
         }

      $let1=0;
      $let3=0;
      $cnt1=0;
      $cnt3=0;
    foreach ($studd as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);
      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
         $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
        ])->update([
           'not4' => number_format($lets1/$lets2,2),
        ]);
         }
////**********************

      $let1=0;
      $cnt1=0;
    foreach ($stude as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);
      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
         $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
        ])->update([
           'not5' => number_format($lets1/$lets2,2),
        ]);
         }
      $let1=0;
      $cnt1=0;
    foreach ($studf as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);
      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
         $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
        ])->update([
           'not6' => number_format($lets1/$lets2,2),
        ]);
         }
      $let1=0;
      $cnt1=0;
    foreach ($studg as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);
      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
         $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
        ])->update([
           'not7' => number_format($lets1/$lets2,2),
        ]);
         }



      $let1=0;
      $cnt1=0;
    foreach ($studh as $stu)
            {
            if ($stu->sem1 > 0){
               $cnt1=$cnt1+$stu->credito;
               IF($stu->sem1 > 89){$let1=$let1+(4*$stu->credito);}
               IF($stu->sem1 > 79 and $stu->sem1 < 90){$let1=$let1+(3*$stu->credito);}
               IF($stu->sem1 > 69 and $stu->sem1 < 80){$let1=$let1+(2*$stu->credito);}
               IF($stu->sem1 > 64 and $stu->sem1 < 70){$let1=$let1+(1*$stu->credito);}
               }
            if ($stu->sem2 > 0){
               $cnt3=$cnt3+$stu->credito;
               IF($stu->sem2 > 89){$let3=$let3+(4*$stu->credito);}
               IF($stu->sem2 > 79 and $stu->sem2 < 90){$let3=$let3+(3*$stu->credito);}
               IF($stu->sem2 > 69 and $stu->sem2 < 80){$let3=$let3+(2*$stu->credito);}
               IF($stu->sem2 > 64 and $stu->sem2 < 70){$let3=$let3+(1*$stu->credito);}
               }
            }
    $acumula = DB::table('acumula_totales')->insert([
        'ss' => $student->ss,
        'nombre' => $student->nombre,
        'apellidos' => $student->apellidos,
    ]);
      if ($cnt1 > 0 or $cnt3 > 0)
         {
         $lets1=0;
         $lets2=0;
         if ($cnt1 > 0)
            {
            $lets1=$lets1+number_format($let1/$cnt1,2);
            $lets2=$lets2+1;
            }
         if ($cnt3 > 0)
            {
            $lets1=$lets1+number_format($let3/$cnt3,2);
            $lets2=$lets2+1;
            }
         if ($lets2 > 0)
            {
            $let2=$let2+number_format($lets1/$lets2,2);
            $cnt2=$cnt2+1;
            }
         $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
        ])->update([
           'not8' => number_format($lets1/$lets2,2),
        ]);
         }

      if ($cnt2> 0)
         {
         $thisCourse2 = DB::table('acumula_totales')->where([
           ['ss', $student->ss]
        ])->update([
           'final' => number_format($let2/$cnt2,2),
        ]);
         }

      }
      

$studz = DB::table('acumula_totales')->orderBy('final DESC')->get();
foreach ($studz as $stu)
        {
   $notas = 0;
   $cantidad = 0;
   if ($grados=='A')
      {
         if($stu->not5 > 0){
            $notas += $stu->not5;
            $cantidad++;
         }
         if($stu->not6 > 0){
            $notas += $stu->not6;
            $cantidad++;
         }
         if($stu->not7 > 0){
            $notas += $stu->not7;
            $cantidad++;
         }
         if($stu->not8 > 0){
            $notas += $stu->not8;
            $cantidad++;
         }
      }
      
         if($stu->not1 > 0){
            $notas += $stu->not1;
            $cantidad++;
         }
         if($stu->not2 > 0){
            $notas += $stu->not2;
            $cantidad++;
         }
         if($stu->not3 > 0){
            $notas += $stu->not3;
            $cantidad++;
         }
         if($stu->not4 > 0){
            $notas += $stu->not4;
            $cantidad++;
         }

if ($cantidad > 0)
   {
$thisCourse7 = DB::table('acumula_totales')->where([
           ['ss', $stu->ss]
        ])->update([
           'final' => number_format($notas/$cantidad,2),
        ]);
   }

}
$q7 = "select * from acumula_totales ORDER BY final DESC";
$studz = DB::table('acumula_totales')->orderBy('final DESC')->get();
$num_resultados = count($studz);
$a=0;
$p=0;
$p2=0;
$not='';
foreach ($studz as $stu)
        {
      $a=$a+1;
      if ($stu->final != $not)
         {
         $p=$p+1+$p2;
         $not = $stu->final;
         $p2=0;
         }
      else
         {
         $p2=$p2+1;
         }
      $pdf->Cell(15,5,$a,0,0,'R');
      $pdf->Cell(25,5,$p.'/'.$num_resultados,0,0,'C');
      $pdf->Cell(90,5,$stu->apellidos.' '.$stu->nombre,0,0,'L');
     
      IF ($grados=='A')
         {
         $pdf->Cell(12,5,number_format($stu->not8,2),0,0,'C');
         $pdf->Cell(12,5,number_format($stu->not7,2),0,0,'C');
         $pdf->Cell(12,5,number_format($stu->not6,2),0,0,'C');
         $pdf->Cell(12,5,number_format($stu->not5,2),0,0,'C');
         }
        
      $pdf->Cell(12,5,number_format($stu->not4,2),0,0,'C');
      $pdf->Cell(12,5,number_format($stu->not3,2),0,0,'C');
      $pdf->Cell(12,5,number_format($stu->not2,2),0,0,'C');
      $pdf->Cell(12,5,number_format($stu->not1,2),0,0,'C');
      $pdf->Cell(12,5,number_format($stu->final,2),0,1,'C');
      }


$pdf->Output();
