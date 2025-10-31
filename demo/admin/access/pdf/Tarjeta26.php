<?php
require_once '../../../app.php';
$anc = 5;

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();

$conducta  = [];
$promedio  = [];
$promedioLetters  = [];
$cant = [];


$lang = new Lang([
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],

]);


//<head>
//<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
//</head>

$school = new School(Session::id());
class nPDF extends PDF
{
    function Header()
    {
    global $idioma;
    global $grado;
    parent::header();
	$this->Ln(-5);
	$this->Cell(80);
	$this->SetFont('Arial','B',11);
    $this->Image('../../../logo/fondo.gif',30,80,150);
	
 if ($idioma=='A')
    {
    $this->Cell(30,10,utf8_encode('TRANSCRIPCIÓN DE CRÉDITOS'),0,0,'C');
    }
 else
    {
    if ($grado=='A')
       {
       $this->Cell(30,10,'ELEMENTAL SCHOOL TRANSCRIPT',0,0,'C');
       }
    if ($grado=='B')
       {
       $this->Cell(30,10,'ELEMENTARY/MIDDLE SCHOOL TRANSCRIPT',0,0,'C');
       }
    if ($grado=='C')
       {
       $this->Cell(30,10,'HIGH SCHOOL TRANSCRIPT',0,0,'C');
       }
    }
	$this->Ln(6);
    $this->Cell(60);
	$this->SetFont('Arial','',9);
    $this->Ln(8);
}

function Footer()
{
    $tem1   =  $_COOKIE["variable13"] ?? '';
    $tem2   =  $_COOKIE["variable14"] ?? '';
    $this->SetY(-25);
    $this->SetFont('Arial','I',8);
    $this->Cell(50,5,'Observaciones:',0,1,'L');
    $this->Cell(190,5,$tem1,'B',1,'L');
    $this->Cell(190,5,$tem2,'B',1,'L');
}
}

function NLetra($valor, $cr, $sem){

     global $colegio;
     global $let1;
     global $cnt1;     
     global $let2;
     global $cnt2;     

  if($valor == ''){
    return '';
  }else if ($valor <= '200' && $valor >= $colegio->vala) {
    if ($cr > 0 and $sem == 1){$let1=$let1+(4*$cr);$cnt1=$cnt1+$cr;}
    if ($cr > 0 and $sem == 2){$let2=$let2+(4*$cr);$cnt2=$cnt2+$cr;}
    return 'A';
  }else if ($valor < $colegio->vala && $valor >= $colegio->valb) {
    if ($cr > 0 and $sem == 1){$let1=$let1+(3*$cr);$cnt1=$cnt1+$cr;}
    if ($cr > 0 and $sem == 2){$let2=$let2+(3*$cr);$cnt2=$cnt2+$cr;}
    return 'B';
  }else if ($valor < $colegio->valb && $valor >= $colegio->valc) {
    if ($cr > 0 and $sem == 1){$let1=$let1+(2*$cr);$cnt1=$cnt1+$cr;}
    if ($cr > 0 and $sem == 2){$let2=$let2+(2*$cr);$cnt2=$cnt2+$cr;}
    return 'C';
  }else if ($valor < $colegio->valc && $valor >= $colegio->vald) {
    if ($cr > 0 and $sem == 1){$let1=$let1+(1*$cr);$cnt1=$cnt1+$cr;}
    if ($cr > 0 and $sem == 2){$let2=$let2+(1*$cr);$cnt2=$cnt2+$cr;}
    return 'D';
  }else  if ($valor < $colegio->vald) {
    if ($cr > 0 and $sem == 1){$let1=$let1+0;$cnt1=$cnt1+$cr;}
    if ($cr > 0 and $sem == 2){$let2=$let2+0;$cnt2=$cnt2+$cr;}
    return 'F';
  }
}

$pdf = new nPDF();
$pdf->Fill();
$pdf->SetLeftMargin(5);
$pdf->AliasNbPages();
$pdf->SetFont('Times','',11);
$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

 IF ($grado=='A')
    {
    $gra1='01-';
    $gra2='02-';
    $gra3='03';
    $gra4='04';
    }
 IF ($grado=='B')
    {
    $gra1='05';
    $gra2='06';
    $gra3='07';
    $gra4='08';
    }
 IF ($grado=='C')
    {
    $gra1='09';
    $gra2='10';
    $gra3='11';
    $gra4='12';
    }
 IF ($grado=='D')
    {
    $gra1='10';
    $gra2='11';
    $gra3='12';
    $gra4='15';
    }

$nm=0;
$ll=1;
$CANT = 0;
$NOTA1 = 0;
$NOTA2 = 0;
$S1 = false;
$S2 = false;

if ($opcion == '2') {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("year = '$Year' and grado = '$grados'")->orderBy('apellidos')->get();
} else {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}

foreach ($students as $estu) {
    $pdf->AddPage();
    $nombre=$estu->ss;
    $ape=$estu->apellidos;
    $nom=$estu->nombre;
    $nm=0;
    $nm7=0;
    $n1=0;$n2=0;
    $NOTAS1=0;
    $NOTAS2=0;

    $rega = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss = '$estu->ss' and grado like '%" . $gra1 . "%'")->orderBy('orden')->get();
    $num_resultados1 = count($rega);

    $regb = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss = '$estu->ss' and grado like '%" . $gra2 . "%'")->orderBy('orden')->get();
    $num_resultados2 = count($regb);

 IF ($num_resultados1 > $num_resultados2)
    {$nm = $num_resultados1;}
  ELSE
    {$nm = $num_resultados2;}

 $nm2=$nm;
 $pdf->SetFont('Arial','',10);

IF ($idioma=='A')
   {
   $nom1 ='NOMBRE';
   $fec ='FECHA';
   $gra ='GRADO';
   $ano =utf8_encode('AÑO');
   $des =utf8_encode('DESCRIPCIÓN');
   $pro ='PROMEDIO GEN.';
   $proa ='PROMEDIO ACU.';
   $pr ='PROMEDIO';
   }
 ELSE
   {
   $nom1 ='NAME';
   $fec ='DATE';
   $gra ='GRADE';
   $ano ='YEAR';
   $des ='DESCRIPTION';
   $pro ='GENERAL AVE.';
   $proa ='CUMULATIVE AVE.';
   $pr ='AVERAGE';
   }
IF ($num_resultados1 + $num_resultados2 > 0)
   {
   $nm7=1;
   $pdf->Cell(5);
   $cursosS1 = array();
   $cursosS2 = array();
   $num = 1;
   foreach ($rega as $row1) {
       $cursosS1[$num] = $row1;
       $num++;
   }
   $num = 1;
   foreach ($regb as $row1) {
       $cursosS2[$num] = $row1;
       $num++;
   }

 IF ($num_resultados1 > $num_resultados2)
    {
    $pdf->Cell(10);
    $pdf->Cell(25,5,$nom1,'LTB',0,'C',true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(90,5,$cursosS1[2]->apellidos.' '.$cursosS1[1]->nombre,'RTB',0,'L',true);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(25,5,$fec,'LTB',0,'C',true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(25,5,DATE('m-d-Y'),'RTB',1,'C',true);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(5);
    }
  ELSE
    {
    $pdf->Cell(10);
    $pdf->Cell(25,5,$nom1,'LTB',0,'C',true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(90,5,$cursosS2[2]->apellidos.' '.$cursosS2[1]->nombre,'RTB',0,'L',true);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(25,5,$fec,'LTB',0,'C',true);
    $pdf->Cell(25,5,DATE('m-d-Y'),'RTB',1,'C',true);
    $pdf->Ln(5);
    }

$pdf->Cell(24,5,$gra,'LTB',0,'C',true);
$gr = substr($cursosS1[4]->grado, 0,2);
$pdf->Cell(24,5,$gr,'RTB',0,'C',true);
$pdf->Cell(22,5,$ano,'LTB',0,'C',true);
$y1 = substr($cursosS1[3]->year, 0,2);
$y2 = substr($cursosS1[3]->year, 3,5);
$pdf->Cell(25,5,"20$y1 20$y2",'RTB',0,'C',true);
//separador
$pdf->Cell(10);
$pdf->Cell(24,5,$gra,'LTB',0,'C',true);
$gr = substr($cursosS2[4]->grado, 0,2);
$pdf->Cell(24,5,$gr,'RTB',0,'C',true);
$pdf->Cell(22,5,$ano,'LTB',0,'C',true);
$y1 = substr($cursosS2[3]->year, 0,2);
$y2 = substr($cursosS2[3]->year, 3,5);
$pdf->Cell(25,5,"20$y1 20$y2",'RTB',1,'C',true);

$pdf->Cell(53,5,$des,1,0,'C',true);
$pdf->Cell(11,5,'SEM-1',1,0,'C',true);
$pdf->Cell(11,5,'SEM-2',1,0,'C',true);
$pdf->Cell(10,5,'FINAL',1,0,'C',true);
$pdf->Cell(10,5,'CRE.',1,0,'C',true);
//separador
$pdf->Cell(10);
$pdf->Cell(53,5,$des,1,0,'C',true);
$pdf->Cell(11,5,'SEM-1',1,0,'C',true);
$pdf->Cell(11,5,'SEM-2',1,0,'C',true);
$pdf->Cell(10,5,'FINAL',1,0,'C',true);
$pdf->Cell(10,5,'CRE.',1,1,'C',true);
$pdf->SetFont('Arial','',9);
$cre1=0;
$cre2=0;
$cre3=0;
$cre4=0;
$cre5=0;
$cre6=0;
$not1=0;
$not2=0;
$not3=0;
$not4=0;
$let1=0;
$let2=0;
$let3=0;
$let4=0;
$let5=0;
$cnt1=0;
$cnt2=0;
$cnt3=0;
$cnt4=0;
$cnt5=0;

$ntf1=0;
$ctf1=0;
$ont1=0;
$ont2=0;

   $pfa1='';
   $pfa2='';
   $pfb1='';
   $pfb2='';
for ($i = 1; $i <= $nm; $i++)       {
      $s1='';
      $s2='';
      $pf='';
      $pf1=0;
      $pf2=0;
      if ($idioma=='A')
         {$pdf->Cell(53,$anc,$cursosS1[$i]->desc1 ?? '',$ll,0,'L');}
       else
         {$pdf->Cell(53,$anc,$cursosS1[$i]->desc2 ?? '',$ll,0,'L');}
      IF ($tnot=='A')
         {
           if ($cursosS1[$i]->credito ?? 0 != 0 && ($cursosS1[$i]->sem1 ?? '' != '' || $cursosS1[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS1[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS1[$i]->sem2 != '') {
             $S2 = true;
           }
         }

         IF($cursosS1[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS1[$i]->sem1;
           }
         IF($cursosS1[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS1[$i]->sem2;  
           }

         $pdf->Cell(11,$anc,NLetra($cursosS1[$i]->sem1 ?? '', 0, 0),$ll,0,'C');
         $pdf->Cell(11,$anc,NLetra($cursosS1[$i]->sem2 ?? '', 0, 0),$ll,0,'C');

         IF ($cursosS1[$i]->sem1 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem1;}
         IF ($cursosS1[$i]->sem2 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,NLetra($pf,$cursosS1[$i]->credito ?? 0, 1),$ll,0,'C');
         $pf='';
         }
      IF ($tnot=='B')
         {
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->valf AND $cursosS1[$i]->sem1 ?? '' < $colegio->vald){$s1='F';IF ($cursosS1[$i]->credito > 0){$let1=$let1+0;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->vald AND $cursosS1[$i]->sem1 ?? '' < $colegio->valc){$s1='D';IF ($cursosS1[$i]->credito > 0){$let1=$let1+1;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->valc AND $cursosS1[$i]->sem1 ?? '' < $colegio->valb){$s1='C';IF ($cursosS1[$i]->credito > 0){$let1=$let1+2;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->valb AND $cursosS1[$i]->sem1 ?? '' < $colegio->vala){$s1='B';IF ($cursosS1[$i]->credito > 0){$let1=$let1+3;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->vala){$s1='A';IF ($cursosS1[$i]->credito > 0){$let1=$let1+4;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->valf AND $cursosS1[$i]->sem2 ?? '' < $colegio->vald){$s2='F';IF ($cursosS1[$i]->credito > 0){$let1=$let1+0;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->vald AND $cursosS1[$i]->sem2 ?? '' < $colegio->valc){$s2='D';IF ($cursosS1[$i]->credito > 0){$let1=$let1+1;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->valc AND $cursosS1[$i]->sem2 ?? '' < $colegio->valb){$s2='C';IF ($cursosS1[$i]->credito > 0){$let1=$let1+2;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->valb AND $cursosS1[$i]->sem2 ?? '' < $colegio->vala){$s2='B';IF ($cursosS1[$i]->credito > 0){$let1=$let1+3;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->vala){$s2='A';IF ($cursosS1[$i]->credito > 0){$let1=$let1+4;$cnt1=$cnt1+1;}}
         if ($cursosS1[$i]->credito ?? 0 != 0 && ($cursosS1[$i]->sem1 ?? '' != '' || $cursosS1[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS1[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS1[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS1[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS1[$i]->sem1;
           }
         IF($cursosS1[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS1[$i]->sem2;  
           }

         $pdf->Cell(11,$anc,$cursosS1[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(11,$anc,$cursosS1[$i]->sem2 ?? '',$ll,0,'R');
         if (is_numeric($cursosS1[$i]->sem1 ?? '') and $cursosS1[$i]->sem1 ?? 0 > 0)
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS1[$i]->sem1;
            }
         if (is_numeric($cursosS1[$i]->sem2 ?? '') and $cursosS1[$i]->sem2 ?? 0 > 0)
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS1[$i]->sem2;
            }
         if ($pf1 > 0){$pf=$pf2/$pf1;}
         if ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,$pf,$ll,0,'R');
         }

      IF ($tnot=='C')
         {
         if ($cursosS1[$i]->credito ?? 0 != 0 && ($cursosS1[$i]->sem1 ?? '' != '' || $cursosS1[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS1[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS1[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS1[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS1[$i]->sem1;
           }
         IF($cursosS1[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS1[$i]->sem2;  
           }

         $pdf->Cell(7,$anc,$cursosS1[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS1[$i]->sem1 ?? '', 0 ,0),$ll,0,'C');
         $pdf->Cell(7,$anc,$cursosS1[$i]->sem2 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS1[$i]->sem2 ?? '', 0 ,0),$ll,0,'C');
         $pf1=0;$pf2=0;
         IF ($cursosS1[$i]->sem1 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem1;}
         IF ($cursosS1[$i]->sem2 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(6,$anc,$pf,$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($pf, $cursosS1[$i]->credito ?? 0, 1),$ll,0,'R');

         }

      IF ($cep=='Si' and $cursosS1[$i]->year==$year)
         {
         $pdf->Cell(10,$anc,$cursosS1[$i]->credito/2,$ll,0,'R');
         }
      else
         {
         $pdf->Cell(10,$anc,$cursosS1[$i]->credito ?? '',$ll,0,'R');
         }
      $pdf->Cell(10);

      IF ($pf > 0 AND $cursosS1[$i]->peso > 0){$pfa1=$pfa1+$pf;$pfa2=$pfa2+1;}
      if ($idioma=='A')
         {$pdf->Cell(53,$anc,$cursosS2[$i]->desc1 ?? '',$ll,0,'L');}
       else
         {$pdf->Cell(53,$anc,$cursosS2[$i]->desc2 ?? '',$ll,0,'L');}
      $s1='';
      $s2='';
      $pf='';
      $pf1=0;
      $pf2=0;
         $pfb1=0;
         $pf='';
         $pfb2=0;
      
      IF ($tnot=='A')
         {
         if ($cursosS2[$i]->credito ?? 0 != 0 && ($cursosS2[$i]->sem1 ?? '' != '' || $cursosS2[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS2[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS2[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS2[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS2[$i]->sem1;
           }
         IF($cursosS2[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS2[$i]->sem2;  
           }

         $pdf->Cell(11,$anc,NLetra($cursosS2[$i]->sem1 ?? '', 0, 0),$ll,0,'C');

         $pf1=0;$pf2=0;$pf=0;
         IF ($cursosS2[$i]->sem1 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem1;}
         IF ($cursosS2[$i]->sem2 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,NLetra($pf,$cursosS2[$i]->credito ?? 0, 2),$ll,0,'C');
         }

      IF ($tnot=='B')
         {
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->valf AND $cursosS2[$i]->sem1 ?? 0 < $colegio->vald){$s1='F';IF ($cursosS2[$i]->credito > 0){$let2=$let2+0;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->vald AND $cursosS2[$i]->sem1 ?? 0 < $colegio->valc){$s1='D';IF ($cursosS2[$i]->credito > 0){$let2=$let2+1;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->valc AND $cursosS2[$i]->sem1 ?? 0 < $colegio->valb){$s1='C';IF ($cursosS2[$i]->credito > 0){$let2=$let2+2;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->valb AND $cursosS2[$i]->sem1 ?? 0 < $colegio->vala){$s1='B';IF ($cursosS2[$i]->credito > 0){$let2=$let2+3;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->vala){$s1='A';IF ($cursosS2[$i]->credito > 0){$let2=$let2+4;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->valf AND $cursosS2[$i]->sem2 ?? 0 < $colegio->vald){$s2='F';IF ($cursosS2[$i]->credito > 0){$let2=$let2+0;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->vald AND $cursosS2[$i]->sem2 ?? 0 < $colegio->valc){$s2='D';IF ($cursosS2[$i]->credito > 0){$let2=$let2+1;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->valc AND $cursosS2[$i]->sem2 ?? 0 < $colegio->valb){$s2='C';IF ($cursosS2[$i]->credito > 0){$let2=$let2+2;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->valb AND $cursosS2[$i]->sem2 ?? 0 < $colegio->vala){$s2='B';IF ($cursosS2[$i]->credito > 0){$let2=$let2+3;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->vala){$s2='A';IF ($cursosS2[$i]->credito > 0){$let2=$let2+4;$cnt2=$cnt2+1;}}
          // Contar
         if ($cursosS2[$i]->credito ?? 0 != 0 && ($cursosS2[$i]->sem1 ?? '' != '' || $cursosS2[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS2[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS2[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS2[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS2[$i]->sem1;
           }
         IF($cursosS2[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS2[$i]->sem2;  
           }
         $pdf->Cell(11,$anc,$cursosS2[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(11,$anc,$cursosS2[$i]->sem2 ?? '',$ll,0,'R');
         IF ($cursosS2[$i]->sem1 ?? 0 > 0 )
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS2[$i]->sem1;
            }
         IF ($cursosS2[$i]->sem2 ?? 0 > 0 )
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS2[$i]->sem2;
            }
         IF ($pf1 > 0){$pf=$pf2/$pf1;}

         $pf1=0;$pf2=0;
         IF ($cursosS2[$i]->sem1 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem1;}
         IF ($cursosS2[$i]->sem2 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,$pf,$ll,0,'R');
         }

      IF ($tnot=='C')
         {
         if ($cursosS2[$i]->credito ?? 0 != 0 && ($cursosS2[$i]->sem1 ?? '' != '' || $cursosS2[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS2[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS2[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS2[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS2[$i]->sem1;
           }
         IF($cursosS2[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS2[$i]->sem2;  
           }

         $pdf->Cell(7,$anc,$cursosS2[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS2[$i]->sem1 ?? '', 0 ,0),$ll,0,'C');
         $pdf->Cell(7,$anc,$cursosS2[$i]->sem2 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS2[$i]->sem2 ?? '', 0 ,0),$ll,0,'C');

         $pf1=0;$pf2=0;
         IF ($cursosS2[$i]->sem1 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem1;}
         IF ($cursosS2[$i]->sem2 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(6,$anc,$pf,$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($pf,$cursosS2[$i]->credito ?? 0,2),$ll,0,'C');
         }

      IF ($cep=='Si' and $cursosS2[$i]->year==$year)
         {
         $pdf->Cell(10,$anc,$cursosS2[$i]->credito/2,$ll,1,'R');
         }
      else
         {
         $pdf->Cell(10,$anc,$cursosS2[$i]->credito ?? '',$ll,1,'R');
         }

      IF ($pf > 0 AND $cursosS2[$i]->peso ?? 0 > 0)
         {
         $pfb1=$pfb1+$pf;
         $pfb2=$pfb2+1;
         }

   IF ($cursosS1[$i]->sem1 ?? 0 > 0 AND $cursosS1[$i]->credito ?? 0 > 0)
      {
      $cre1=$cre1+1;
      $not1=$not1+$cursosS1[$i]->sem1;
      }
   IF ($cursosS1[$i]->sem2 ?? 0 > 0 AND $cursosS1[$i]->credito ?? 0 > 0)
      {
      $cre1=$cre1+1;
      $not1=$not1+$cursosS1[$i]->sem2;
      }
   IF ($cursosS2[$i]->sem1 ?? 0 > 0 AND $cursosS2[$i]->credito ?? 0 > 0)
      {
      $cre2=$cre2+1;
      $not2=$not2+$cursosS2[$i]->sem1;
      }
   IF ($cursosS2[$i]->sem2 ?? 0 > 0 AND $cursosS2[$i]->credito ?? 0 > 0)
      {
      $cre2=$cre2+1;
      $not2=$not2+$cursosS2[$i]->sem2;
      }
  if ($cursosS1[$i]->credito ?? 0 > 0)
     {
     $cre5=$cre5+$cursosS1[$i]->credito;
     }
  if ($cursosS2[$i]->credito ?? 0 > 0)
     {
     $cre6=$cre6+$cursosS2[$i]->credito;
     }
}

  $pdf->Cell(53,$anc,$pr,$ll,0,'R',true);
  IF ($cre1 > 0 or $cnt1 > 0)
     {
     if ($tnot=='A')
        {$pdf->Cell(28,$anc,$let1.' / '.$cnt1.'  '.number_format($let1/$cnt1, 2),$ll,0,'C',true);
         $ntf1=$ntf1+number_format($let1/$cnt1, 2);
         $ctf1=$ctf1+1;
        }
     else
        if ($tnot=='C')
           {
            $pdf->Cell(28,$anc,number_format($not1/$cre1, 2).' / '.number_format($let1/$cnt1, 2),$ll,0,'C',true);
            $ont1=$ont1+$not1;
            $ont2=$ont2+$cre1;
            $ntf1=$ntf1+number_format($not1/$cre1, 2);
            $ctf1=$ctf1+1;
           }
        else
           {$pdf->Cell(28,$anc,number_format($not1/$cre1, 2),$ll,0,'C',true);
            $ntf1=$ntf1+number_format($not1/$cre1, 2);
            $ctf1=$ctf1+1;
           }
     }
  else
     {$pdf->Cell(28,$anc,'XXXX',$ll,0,'C',true);}


  IF ($cre5 > 0)
     {
     IF ($cep=='Si' and $cursosS1[3]->year==$year)
        {
        $pdf->Cell(14,$anc,number_format($cre5/2, 2),$ll,0,'R',true);
        }
     else
        {
        $pdf->Cell(14,$anc,number_format($cre5, 2),$ll,0,'R',true);
        }
     }
  else
     {$pdf->Cell(14,$anc,'XXXX',$ll,0,'R',true);}
//separador
     $pdf->Cell(10);


  $pdf->Cell(53,$anc,$pr,$ll,0,'R',true);
  IF ($cre2 > 0 or $cnt2 > 0)
     {
     if ($tnot=='A')
        {$pdf->Cell(28,$anc,$let2.' / '.$cnt2.'  '.number_format($let2/$cnt2, 2),$ll,0,'C',true);
         $ntf1=$ntf1+number_format($let2/$cnt2, 2);
         $ctf1=$ctf1+1;
        }
     else
        if ($tnot=='C')
           {$pdf->Cell(28,$anc,number_format($not2/$cre2, 2).' / '.number_format($let2/$cnt2, 2),$ll,0,'C',true);
            $ont1=$ont1+$not2;
            $ont2=$ont2+$cre2;
          
            $ntf1=$ntf1+number_format($not2/$cre2, 2);
            $ctf1=$ctf1+1;
           }
        else
           {$pdf->Cell(28,$anc,number_format($not2/$cre2, 2),$ll,0,'C',true);
            $ntf1=$ntf1+number_format($not2/$cre2, 2);
            $ctf1=$ctf1+1;
           }
     }
  else
     {$pdf->Cell(28,$anc,'XXXX',$ll,0,'C',true);}
  IF ($cre6 > 0)
     {
     IF ($cep=='Si' and $cursosS2[3]->year == $year)
        {
        $pdf->Cell(14,$anc,number_format($cre6/2, 2),$ll,0,'R',true);
        }
     else
        {
        $pdf->Cell(14,$anc,number_format($cre6, 2),$ll,0,'R',true);
        }
     }
  else
     {$pdf->Cell(14,$anc,'XXXX',$ll,1,'R',true);}
$let5=$let5+$let1+$let2;
$cnt5=$cnt5+$cnt1+$cnt2;

if ($cre1 > 0)
   {$n1 = $n1 + number_format($not1/$cre1,2);$n2=$n2+1;}
if ($cre2 > 0)
   {$n1 = $n1 + number_format($not2/$cre2,2);$n2=$n2+1;}

  $not1=0;
  $not2=0;
  $not3=0;
  $not4=0;
  $acu1=0;
  $acu2=0;
}

//************************************************************************************************************************
$nm=0;
    $rega = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss = '$estu->ss' and grado like '%" . $gra3 . "%'")->orderBy('orden')->get();
    $num_resultados1 = count($rega);

    $regb = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss = '$estu->ss' and grado like '%" . $gra4 . "%'")->orderBy('orden')->get();
    $num_resultados2 = count($regb);

 IF ($num_resultados1 > $num_resultados2)
    {$nm = $num_resultados1;}
  ELSE
    {$nm = $num_resultados2;}

 $nm2=$nm;
 $pdf->SetFont('Arial','',10);

IF ($idioma=='A')
   {
   $nom1 ='NOMBRE';
   $fec ='FECHA';
   $gra ='GRADO';
   $ano =utf8_encode('AÑO');
   $des =utf8_encode('DESCRIPCIÓN');
   $pro ='PROMEDIO GEN.';
   $proa ='PROMEDIO ACU.';
   $pr ='PROMEDIO';
   }
 ELSE
   {
   $nom1 ='NAME';
   $fec ='DATE';
   $gra ='GRADE';
   $ano ='YEAR';
   $des ='DESCRIPTION';
   $pro ='GENERAL AVE.';
   $proa ='CUMULATIVE AVE.';
   $pr ='AVERAGE';
   }
IF ($num_resultados1 + $num_resultados2 > 0)
   {
   $nm7=1;
   $pdf->Cell(5);
   $cursosS1 = array();
   $cursosS2 = array();
   $num = 1;
   foreach ($rega as $row1) {
       $cursosS1[$num] = $row1;
       $num++;
   }
   $num = 1;
   foreach ($regb as $row1) {
       $cursosS2[$num] = $row1;
       $num++;
   }

 IF ($num_resultados1 > $num_resultados2)
    {
    $pdf->Cell(10);
    $pdf->Cell(25,5,$nom1,'LTB',0,'C',true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(90,5,$cursosS1[2]->apellidos.' '.$cursosS1[1]->nombre,'RTB',0,'L',true);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(25,5,$fec,'LTB',0,'C',true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(25,5,DATE('m-d-Y'),'RTB',1,'C',true);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(5);
    }
  ELSE
    {
    $pdf->Cell(10);
    $pdf->Cell(25,5,$nom1,'LTB',0,'C',true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(90,5,$cursosS2[2]->apellidos.' '.$cursosS2[1]->nombre,'RTB',0,'L',true);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(25,5,$fec,'LTB',0,'C',true);
    $pdf->Cell(25,5,DATE('m-d-Y'),'RTB',1,'C',true);
    $pdf->Ln(5);
    }

$pdf->Cell(24,5,$gra,'LTB',0,'C',true);
$gr = substr($cursosS1[4]->grado, 0,2);
$pdf->Cell(24,5,$gr,'RTB',0,'C',true);
$pdf->Cell(22,5,$ano,'LTB',0,'C',true);
$y1 = substr($cursosS1[3]->year, 0,2);
$y2 = substr($cursosS1[3]->year, 3,5);
$pdf->Cell(25,5,"20$y1 20$y2",'RTB',0,'C',true);
//separador
$pdf->Cell(10);
$pdf->Cell(24,5,$gra,'LTB',0,'C',true);
$gr = substr($cursosS2[4]->grado, 0,2);
$pdf->Cell(24,5,$gr,'RTB',0,'C',true);
$pdf->Cell(22,5,$ano,'LTB',0,'C',true);
$y1 = substr($cursosS2[3]->year, 0,2);
$y2 = substr($cursosS2[3]->year, 3,5);
$pdf->Cell(25,5,"20$y1 20$y2",'RTB',1,'C',true);

$pdf->Cell(53,5,$des,1,0,'C',true);
$pdf->Cell(11,5,'SEM-1',1,0,'C',true);
$pdf->Cell(11,5,'SEM-2',1,0,'C',true);
$pdf->Cell(10,5,'FINAL',1,0,'C',true);
$pdf->Cell(10,5,'CRE.',1,0,'C',true);
//separador
$pdf->Cell(10);
$pdf->Cell(53,5,$des,1,0,'C',true);
$pdf->Cell(11,5,'SEM-1',1,0,'C',true);
$pdf->Cell(11,5,'SEM-2',1,0,'C',true);
$pdf->Cell(10,5,'FINAL',1,0,'C',true);
$pdf->Cell(10,5,'CRE.',1,1,'C',true);
$pdf->SetFont('Arial','',9);
$cre1=0;
$cre2=0;
$cre3=0;
$cre4=0;
$cre5=0;
$cre6=0;
$not1=0;
$not2=0;
$not3=0;
$not4=0;
$let1=0;
$let2=0;
$let3=0;
$let4=0;
$let5=0;
$cnt1=0;
$cnt2=0;
$cnt3=0;
$cnt4=0;
$cnt5=0;

$ntf1=0;
$ctf1=0;
$ont1=0;
$ont2=0;

   $pfa1='';
   $pfa2='';
   $pfb1='';
   $pfb2='';
for ($i = 1; $i <= $nm; $i++)       {
      $s1='';
      $s2='';
      $pf='';
      $pf1=0;
      $pf2=0;
      if ($idioma=='A')
         {$pdf->Cell(53,$anc,$cursosS1[$i]->desc1 ?? '',$ll,0,'L');}
       else
         {$pdf->Cell(53,$anc,$cursosS1[$i]->desc2 ?? '',$ll,0,'L');}
      IF ($tnot=='A')
         {
           if ($cursosS1[$i]->credito ?? 0 != 0 && ($cursosS1[$i]->sem1 ?? '' != '' || $cursosS1[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS1[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS1[$i]->sem2 != '') {
             $S2 = true;
           }
         }

         IF($cursosS1[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS1[$i]->sem1;
           }
         IF($cursosS1[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS1[$i]->sem2;  
           }

         $pdf->Cell(11,$anc,NLetra($cursosS1[$i]->sem1 ?? '', 0, 0),$ll,0,'C');
         $pdf->Cell(11,$anc,NLetra($cursosS1[$i]->sem2 ?? '', 0, 0),$ll,0,'C');

         IF ($cursosS1[$i]->sem1 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem1;}
         IF ($cursosS1[$i]->sem2 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,NLetra($pf,$cursosS1[$i]->credito ?? 0, 1),$ll,0,'C');
         $pf='';
         }
      IF ($tnot=='B')
         {
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->valf AND $cursosS1[$i]->sem1 ?? '' < $colegio->vald){$s1='F';IF ($cursosS1[$i]->credito > 0){$let1=$let1+0;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->vald AND $cursosS1[$i]->sem1 ?? '' < $colegio->valc){$s1='D';IF ($cursosS1[$i]->credito > 0){$let1=$let1+1;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->valc AND $cursosS1[$i]->sem1 ?? '' < $colegio->valb){$s1='C';IF ($cursosS1[$i]->credito > 0){$let1=$let1+2;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->valb AND $cursosS1[$i]->sem1 ?? '' < $colegio->vala){$s1='B';IF ($cursosS1[$i]->credito > 0){$let1=$let1+3;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem1 ?? '' >= $colegio->vala){$s1='A';IF ($cursosS1[$i]->credito > 0){$let1=$let1+4;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->valf AND $cursosS1[$i]->sem2 ?? '' < $colegio->vald){$s2='F';IF ($cursosS1[$i]->credito > 0){$let1=$let1+0;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->vald AND $cursosS1[$i]->sem2 ?? '' < $colegio->valc){$s2='D';IF ($cursosS1[$i]->credito > 0){$let1=$let1+1;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->valc AND $cursosS1[$i]->sem2 ?? '' < $colegio->valb){$s2='C';IF ($cursosS1[$i]->credito > 0){$let1=$let1+2;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->valb AND $cursosS1[$i]->sem2 ?? '' < $colegio->vala){$s2='B';IF ($cursosS1[$i]->credito > 0){$let1=$let1+3;$cnt1=$cnt1+1;}}
         IF ($cursosS1[$i]->sem2 ?? '' >= $colegio->vala){$s2='A';IF ($cursosS1[$i]->credito > 0){$let1=$let1+4;$cnt1=$cnt1+1;}}
         if ($cursosS1[$i]->credito ?? 0 != 0 && ($cursosS1[$i]->sem1 ?? '' != '' || $cursosS1[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS1[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS1[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS1[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS1[$i]->sem1;
           }
         IF($cursosS1[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS1[$i]->sem2;  
           }

         $pdf->Cell(11,$anc,$cursosS1[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(11,$anc,$cursosS1[$i]->sem2 ?? '',$ll,0,'R');
         if (is_numeric($cursosS1[$i]->sem1 ?? '') and $cursosS1[$i]->sem1 ?? 0 > 0)
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS1[$i]->sem1;
            }
         if (is_numeric($cursosS1[$i]->sem2 ?? '') and $cursosS1[$i]->sem2 ?? 0 > 0)
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS1[$i]->sem2;
            }
         if ($pf1 > 0){$pf=$pf2/$pf1;}
         if ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,$pf,$ll,0,'R');
         }

      IF ($tnot=='C')
         {
         if ($cursosS1[$i]->credito ?? 0 != 0 && ($cursosS1[$i]->sem1 ?? '' != '' || $cursosS1[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS1[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS1[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS1[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS1[$i]->sem1;
           }
         IF($cursosS1[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS1[$i]->sem2;  
           }

         $pdf->Cell(7,$anc,$cursosS1[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS1[$i]->sem1 ?? '', 0 ,0),$ll,0,'C');
         $pdf->Cell(7,$anc,$cursosS1[$i]->sem2 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS1[$i]->sem2 ?? '', 0 ,0),$ll,0,'C');
         $pf1=0;$pf2=0;
         IF ($cursosS1[$i]->sem1 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem1;}
         IF ($cursosS1[$i]->sem2 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS1[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(6,$anc,$pf,$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($pf, $cursosS1[$i]->credito ?? 0, 1),$ll,0,'R');

         }

      IF ($cep=='Si' and $cursosS1[$i]->year==$year)
         {
         $pdf->Cell(10,$anc,$cursosS1[$i]->credito/2,$ll,0,'R');
         }
      else
         {
         $pdf->Cell(10,$anc,$cursosS1[$i]->credito ?? '',$ll,0,'R');
         }
      $pdf->Cell(10);

      IF ($pf > 0 AND $cursosS1[$i]->peso > 0){$pfa1=$pfa1+$pf;$pfa2=$pfa2+1;}
      if ($idioma=='A')
         {$pdf->Cell(53,$anc,$cursosS2[$i]->desc1 ?? '',$ll,0,'L');}
       else
         {$pdf->Cell(53,$anc,$cursosS2[$i]->desc2 ?? '',$ll,0,'L');}
      $s1='';
      $s2='';
      $pf='';
      $pf1=0;
      $pf2=0;
         $pfb1=0;
         $pf='';
         $pfb2=0;
      
      IF ($tnot=='A')
         {
         if ($cursosS2[$i]->credito ?? 0 != 0 && ($cursosS2[$i]->sem1 ?? '' != '' || $cursosS2[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS2[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS2[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS2[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS2[$i]->sem1;
           }
         IF($cursosS2[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS2[$i]->sem2;  
           }

         $pdf->Cell(11,$anc,NLetra($cursosS2[$i]->sem1 ?? '', 0, 0),$ll,0,'C');

         $pf1=0;$pf2=0;$pf=0;
         IF ($cursosS2[$i]->sem1 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem1;}
         IF ($cursosS2[$i]->sem2 ?? '' > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,NLetra($pf,$cursosS2[$i]->credito ?? 0, 2),$ll,0,'C');
         }

      IF ($tnot=='B')
         {
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->valf AND $cursosS2[$i]->sem1 ?? 0 < $colegio->vald){$s1='F';IF ($cursosS2[$i]->credito > 0){$let2=$let2+0;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->vald AND $cursosS2[$i]->sem1 ?? 0 < $colegio->valc){$s1='D';IF ($cursosS2[$i]->credito > 0){$let2=$let2+1;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->valc AND $cursosS2[$i]->sem1 ?? 0 < $colegio->valb){$s1='C';IF ($cursosS2[$i]->credito > 0){$let2=$let2+2;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->valb AND $cursosS2[$i]->sem1 ?? 0 < $colegio->vala){$s1='B';IF ($cursosS2[$i]->credito > 0){$let2=$let2+3;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem1 ?? 0 >= $colegio->vala){$s1='A';IF ($cursosS2[$i]->credito > 0){$let2=$let2+4;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->valf AND $cursosS2[$i]->sem2 ?? 0 < $colegio->vald){$s2='F';IF ($cursosS2[$i]->credito > 0){$let2=$let2+0;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->vald AND $cursosS2[$i]->sem2 ?? 0 < $colegio->valc){$s2='D';IF ($cursosS2[$i]->credito > 0){$let2=$let2+1;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->valc AND $cursosS2[$i]->sem2 ?? 0 < $colegio->valb){$s2='C';IF ($cursosS2[$i]->credito > 0){$let2=$let2+2;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->valb AND $cursosS2[$i]->sem2 ?? 0 < $colegio->vala){$s2='B';IF ($cursosS2[$i]->credito > 0){$let2=$let2+3;$cnt2=$cnt2+1;}}
         IF ($cursosS2[$i]->sem2 ?? 0 >= $colegio->vala){$s2='A';IF ($cursosS2[$i]->credito > 0){$let2=$let2+4;$cnt2=$cnt2+1;}}
          // Contar
         if ($cursosS2[$i]->credito ?? 0 != 0 && ($cursosS2[$i]->sem1 ?? '' != '' || $cursosS2[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS2[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS2[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS2[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS2[$i]->sem1;
           }
         IF($cursosS2[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS2[$i]->sem2;  
           }
         $pdf->Cell(11,$anc,$cursosS2[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(11,$anc,$cursosS2[$i]->sem2 ?? '',$ll,0,'R');
         IF ($cursosS2[$i]->sem1 ?? 0 > 0 )
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS2[$i]->sem1;
            }
         IF ($cursosS2[$i]->sem2 ?? 0 > 0 )
            {
            $pf1=$pf1+1;
            $pf2=$pf2+$cursosS2[$i]->sem2;
            }
         IF ($pf1 > 0){$pf=$pf2/$pf1;}

         $pf1=0;$pf2=0;
         IF ($cursosS2[$i]->sem1 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem1;}
         IF ($cursosS2[$i]->sem2 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(10,$anc,$pf,$ll,0,'R');
         }

      IF ($tnot=='C')
         {
         if ($cursosS2[$i]->credito ?? 0 != 0 && ($cursosS2[$i]->sem1 ?? '' != '' || $cursosS2[$i]->sem2 ?? '' != '')) {
           $CANT++;
            if ($cursosS2[$i]->sem1 != '') {
             $S1 = true;
           }
            if ($cursosS2[$i]->sem2 != '') {
             $S2 = true;
           }
         }
         IF($cursosS2[$i]->sem1 ?? 0 > 0)
           {
           $NOTAS1 += $cursosS2[$i]->sem1;
           }
         IF($cursosS2[$i]->sem2 ?? 0 > 0)
           {
           $NOTAS2 += $cursosS2[$i]->sem2;  
           }

         $pdf->Cell(7,$anc,$cursosS2[$i]->sem1 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS2[$i]->sem1 ?? '', 0 ,0),$ll,0,'C');
         $pdf->Cell(7,$anc,$cursosS2[$i]->sem2 ?? '',$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($cursosS2[$i]->sem2 ?? '', 0 ,0),$ll,0,'C');

         $pf1=0;$pf2=0;
         IF ($cursosS2[$i]->sem1 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem1;}
         IF ($cursosS2[$i]->sem2 ?? 0 > 0 ){$pf1=$pf1+1;$pf2=$pf2+$cursosS2[$i]->sem2;}
         IF ($pf1 > 0){$pf=round($pf2/$pf1,0);}
         $pdf->Cell(6,$anc,$pf,$ll,0,'R');
         $pdf->Cell(4,$anc,NLetra($pf,$cursosS2[$i]->credito ?? 0,2),$ll,0,'C');
         }

      IF ($cep=='Si' and $cursosS2[$i]->year==$year)
         {
         $pdf->Cell(10,$anc,$cursosS2[$i]->credito/2,$ll,1,'R');
         }
      else
         {
         $pdf->Cell(10,$anc,$cursosS2[$i]->credito ?? '',$ll,1,'R');
         }

      IF ($pf > 0 AND $cursosS2[$i]->peso ?? 0 > 0)
         {
         $pfb1=$pfb1+$pf;
         $pfb2=$pfb2+1;
         }

   IF ($cursosS1[$i]->sem1 ?? 0 > 0 AND $cursosS1[$i]->credito ?? 0 > 0)
      {
      $cre1=$cre1+1;
      $not1=$not1+$cursosS1[$i]->sem1;
      }
   IF ($cursosS1[$i]->sem2 ?? 0 > 0 AND $cursosS1[$i]->credito ?? 0 > 0)
      {
      $cre1=$cre1+1;
      $not1=$not1+$cursosS1[$i]->sem2;
      }
   IF ($cursosS2[$i]->sem1 ?? 0 > 0 AND $cursosS2[$i]->credito ?? 0 > 0)
      {
      $cre2=$cre2+1;
      $not2=$not2+$cursosS2[$i]->sem1;
      }
   IF ($cursosS2[$i]->sem2 ?? 0 > 0 AND $cursosS2[$i]->credito ?? 0 > 0)
      {
      $cre2=$cre2+1;
      $not2=$not2+$cursosS2[$i]->sem2;
      }
  if ($cursosS1[$i]->credito ?? 0 > 0)
     {
     $cre5=$cre5+$cursosS1[$i]->credito;
     }
  if ($cursosS2[$i]->credito ?? 0 > 0)
     {
     $cre6=$cre6+$cursosS2[$i]->credito;
     }
}

  $pdf->Cell(53,$anc,$pr,$ll,0,'R',true);
  IF ($cre1 > 0 or $cnt1 > 0)
     {
     if ($tnot=='A')
        {$pdf->Cell(28,$anc,$let1.' / '.$cnt1.'  '.number_format($let1/$cnt1, 2),$ll,0,'C',true);
         $ntf1=$ntf1+number_format($let1/$cnt1, 2);
         $ctf1=$ctf1+1;
        }
     else
        if ($tnot=='C')
           {
            $pdf->Cell(28,$anc,number_format($not1/$cre1, 2).' / '.number_format($let1/$cnt1, 2),$ll,0,'C',true);
            $ont1=$ont1+$not1;
            $ont2=$ont2+$cre1;
            $ntf1=$ntf1+number_format($not1/$cre1, 2);
            $ctf1=$ctf1+1;
           }
        else
           {$pdf->Cell(28,$anc,number_format($not1/$cre1, 2),$ll,0,'C',true);
            $ntf1=$ntf1+number_format($not1/$cre1, 2);
            $ctf1=$ctf1+1;
           }
     }
  else
     {$pdf->Cell(28,$anc,'XXXX',$ll,0,'C',true);}


  IF ($cre5 > 0)
     {
     IF ($cep=='Si' and $cursosS1[3]->year==$year)
        {
        $pdf->Cell(14,$anc,number_format($cre5/2, 2),$ll,0,'R',true);
        }
     else
        {
        $pdf->Cell(14,$anc,number_format($cre5, 2),$ll,0,'R',true);
        }
     }
  else
     {$pdf->Cell(14,$anc,'XXXX',$ll,0,'R',true);}
//separador
     $pdf->Cell(10);


  $pdf->Cell(53,$anc,$pr,$ll,0,'R',true);
  IF ($cre2 > 0 or $cnt2 > 0)
     {
     if ($tnot=='A')
        {$pdf->Cell(28,$anc,$let2.' / '.$cnt2.'  '.number_format($let2/$cnt2, 2),$ll,0,'C',true);
         $ntf1=$ntf1+number_format($let2/$cnt2, 2);
         $ctf1=$ctf1+1;
        }
     else
        if ($tnot=='C')
           {$pdf->Cell(28,$anc,number_format($not2/$cre2, 2).' / '.number_format($let2/$cnt2, 2),$ll,0,'C',true);
            $ont1=$ont1+$not2;
            $ont2=$ont2+$cre2;
          
            $ntf1=$ntf1+number_format($not2/$cre2, 2);
            $ctf1=$ctf1+1;
           }
        else
           {$pdf->Cell(28,$anc,number_format($not2/$cre2, 2),$ll,0,'C',true);
            $ntf1=$ntf1+number_format($not2/$cre2, 2);
            $ctf1=$ctf1+1;
           }
     }
  else
     {$pdf->Cell(28,$anc,'XXXX',$ll,0,'C',true);}
  IF ($cre6 > 0)
     {
     IF ($cep=='Si' and $cursosS2[3]->year == $year)
        {
        $pdf->Cell(14,$anc,number_format($cre6/2, 2),$ll,0,'R',true);
        }
     else
        {
        $pdf->Cell(14,$anc,number_format($cre6, 2),$ll,0,'R',true);
        }
     }
  else
     {$pdf->Cell(14,$anc,'XXXX',$ll,1,'R',true);}
$let5=$let5+$let1+$let2;
$cnt5=$cnt5+$cnt1+$cnt2;

if ($cre1 > 0)
   {$n1 = $n1 + number_format($not1/$cre1,2);$n2=$n2+1;}
if ($cre2 > 0)
   {$n1 = $n1 + number_format($not2/$cre2,2);$n2=$n2+1;}

  $not1=0;
  $not2=0;
  $not3=0;
  $not4=0;
  $acu1=0;
  $acu2=0;
}




//***************************************************************

$NOTAS = 0; 
$d = 0;
if ($S1) {
  $NOTAS = $NOTAS1;
  $d = 1;
}
if ($S2) {
  $NOTAS += $NOTAS2;
  $d = 2;
}
if ($d > 0)
{$NOTAS = $NOTAS/$d;}

$pdf->Ln(7);
$pdf->Cell(60);
 if ($idioma=='A' )
    {
     $pdf->SetFont('Arial','B',10);
     $pdf->Cell(53,5,"PROMEDIO ACUMULATIVO ",1,0,'R',true);
     if ($ctf1 > 0)
        {
        if ($tnot=='C')
           {
           $pdf->Cell(28,5,number_format($ntf1/$ctf1, 2) .' / '.number_format($let5/$cnt5, 2),1,1,'C');
           }
        else
           {
           if ($tnot=='B')
              {
              $pdf->Cell(28,5,number_format($let5/$cnt5, 2),1,1,'C');
              }
           else
              {
              $pdf->Cell(28,5,number_format($ntf1/$ctf1, 2),1,1,'C');
              }
           }
        }
     else
        {
        $pdf->Cell(28,5,'',1,1,'R');
        }

     $pdf->Ln(3);
     $pdf->Cell(60);
     $pdf->Cell(53,5,utf8_encode('FECHA DE GRADUACIÓN'),1,0,'R',true);
     $pdf->Cell(28,5,$fec7 ?? '',1,1,'R');
     $pdf->SetFont('Arial','',9);
     $pdf->SetY(-55);
     $pdf->Cell(40,5,'',0,1);
     $pdf->Cell(70,5,'Firma Autorizada','T',0,'C');
     $pdf->Cell(70,5,'',0,0);
     $pdf->Cell(40,5,'Sello Oficial',0,1);
     $pdf->Ln(5);
     $pdf->Cell(25,5,'',0,1);
     $pdf->Cell(70,5,'Puesto','T',1,'C');
    }
 else
    {
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(53,$anc,'ACCUMULATIVE AVERAGE',1,0,'R',true);
    if ($cnt5 > 0)
       {
       if ($tnot=='C')
          {
          $pdf->Cell(28,5,number_format($ntf1/$ctf1, 2) .' / '.number_format($let5/$cnt5, 2),1,1,'C');
          }
       else
          {
          if ($tnot=='B')
             {
             $pdf->Cell(28,5,number_format($let5/$cnt5, 2),1,1,'C');
             }
          else
             {
             $pdf->Cell(28,5,number_format($ntf1/$ctf1, 2),1,1,'C');
             }
          }
        }
     else
        {
        $pdf->Cell(28,$anc,'',1,1,'R');
        }
     $pdf->SetFont('Arial','',9);
     $pdf->SetY(-55);
     $pdf->Cell(40,5,'',0,1);
     $pdf->Cell(70,5,'Authorized Signature','T',0,'C');
     $pdf->Cell(70,5,'',0,0);
     $pdf->Cell(40,5,'Official Stamp',0,1);
     $pdf->Ln(10);
     $pdf->Cell(40,8,'',0,1);
     $pdf->Cell(70,5,'Position','T',1,'C');
    }
    $lo='Si';
    if ($lo=='Si')
       {
       $pdf->Image('../../../logo/sello.jpg',135,238,45);
       }

}
$pdf->Output();
