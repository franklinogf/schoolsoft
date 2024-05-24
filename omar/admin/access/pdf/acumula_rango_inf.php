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

//class PDF extends FPDF
class nPDF extends PDF
{

//Cabecera de p&#65533;gina
function Header()
{
    list($ape,$nom) = explode(", ",$_POST[nombre]);
	list($ape,$nom,$cta) = explode(", ", $_POST['estudiantes']);
	
	//Logo
	$sp=80;
	//Movernos a la derecha
	$this->Cell($sp);
	//Ttulo
	$this->Ln(10);
	$this->Cell($sp);
	$this->SetFont('Arial','B',11);
    $this->Cell(30,5,'LISTA DE PROMEDIOS '.$_POST[Year],0,1,'C');
	$this->Ln(5);
	$this->Cell($sp);
    if($_POST['gr1']==1){$g1=$g1.' 01';}
    if($_POST['gr2']==1){$g1=$g1.' 02';}
    if($_POST['gr3']==1){$g1=$g1.' 03';}
    if($_POST['gr4']==1){$g1=$g1.' 04';}
    if($_POST['gr5']==1){$g1=$g1.' 05';}
    if($_POST['gr6']==1){$g1=$g1.' 06';}
    if($_POST['gr7']==1){$g1=$g1.' 07';}
    if($_POST['gr8']==1){$g1=$g1.' 08';}
    if($_POST['gr9']==1){$g1=$g1.' 09';}
    if($_POST['gr10']==1){$g1=$g1.' 10';}
    if($_POST['gr11']==1){$g1=$g1.' 11';}
    if($_POST['gr12']==1){$g1=$g1.' 12';}
    $this->Cell(30,5,'GRADOS '.$g1,0,1,'C');
    if (!empty($_POST['mat1']) or !empty($_POST['mat2']) or !empty($_POST['mat3']) or !empty($_POST['mat4']))
       {
	   $this->Cell($sp);
       $this->Cell(30,5,'CURSOS '.$_POST['mat1'].' '.$_POST['mat2'].' '.$_POST['mat3'].' '.$_POST['mat4'],0,1,'C');
//       $this->Ln(5);
       }
	$this->Ln(5);
	$this->SetFont('Arial','B',11);
//	$this->SetFillColor(230);
	$this->Cell(75,5,'NOMBRE DEL ESTUDIANTE',1,0,'C',true);
	$this->Cell(10,5,'T-A',1,0,'C',true);
	$this->Cell(10,5,'T-B',1,0,'C',true);
	$this->Cell(10,5,'T-C',1,0,'C',true);
	$this->Cell(10,5,'T-D',1,0,'C',true);
	$this->Cell(10,5,'T-F',1,0,'C',true);
	$this->Cell(10,5,'T-N',1,0,'C',true);
	$this->Cell(15,5,'PROM',1,0,'C',true);
	$this->Cell(13,5,'%',1,0,'C',true);
	$this->Cell(28,5,'N.Est.',1,1,'C',true);
//    $this->Ln(10);

}

//Pie de p&#65533;gina
function Footer()
{

    //Posici&oacute;n: a 1,5 cm del final
    $this->SetY(-15);

    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //N&uacute;mero de p&aacute;gina
   $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}'.' / '.date('m-d-Y'),0,0,'C');

}

}

//Creaci&#65533;n del objeto de la clase heredada
//$pdf=new PDF();
$pdf = new nPDF();
$pdf->SetTitle('INFORME PROMEDIO POR CLASE ACUMULADO');
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',12);

session_start();
$id=$_SESSION['id1'];
$usua=$_SESSION['usua1'];
$year=$_POST['Year'];
//NEW---->
$g1='aa-';$g2='aa-';$g3='aa-';$g4='aa-';$g5='aa-';$g6='aa-';$g7='aa-';$g8='aa-';$g9='aa-';$g10='aa-';$g11='aa-';$g12='aa-';
if($_POST['gr1']==1){$g1='01-';}
if($_POST['gr2']==1){$g2='02-';}
if($_POST['gr3']==1){$g3='03-';}
if($_POST['gr4']==1){$g4='04-';}
if($_POST['gr5']==1){$g5='05-';}
if($_POST['gr6']==1){$g6='06-';}
if($_POST['gr7']==1){$g7='07-';}
if($_POST['gr8']==1){$g8='08-';}
if($_POST['gr9']==1){$g9='09-';}
if($_POST['gr10']==1){$g10='10-';}
if($_POST['gr11']==1){$g11='11-';}
if($_POST['gr12']==1){$g12='12-';}
$grado = $_POST[grade];
if ($_POST['gra']==1)
   {
   list($g,$s) = explode("-",$grado);
   $sSQL1="select * from year where year = '$year' and grado LIKE '%$_POST[grado]%' and activo = '' Order By apellidos";
   $students = DB::table('year')
   ->whereRaw("year = '$year' and grado like '%".$g."%' and activo = ''" )->orderBy('apellidos')->get();
   }
else
   {
   $sSQL1="select * from year where year = '$year' and grado LIKE '$_POST[grado]' and activo = '' Order By apellidos";
   $students = DB::table('year')
   ->whereRaw("year = '$year' and grado like '$grado' and activo = ''" )->orderBy('apellidos')->get();
   }
foreach ($students as $student) 
        {
      $cc1=0;
      if (empty($_POST['mat1']) and empty($_POST['mat2']) and empty($_POST['mat3']) and empty($_POST['mat4']))
         {
         $sSQL2="select * from acumulativa where ss = '$row5[0]' and grado LIKE '%$g1%' or ss = '$row5[0]' and grado LIKE '%$g2%' or ss = '$row5[0]' and grado LIKE '%$g3%' or ss = '$row5[0]' and grado LIKE '%$g4%' or ss = '$row5[0]' and grado LIKE '%$g5%' or ss = '$row5[0]' and grado LIKE '%$g6%' or ss = '$row5[0]' and grado LIKE '%$g7%' or ss = '$row5[0]' and grado LIKE '%$g8%' or ss = '$row5[0]' and grado LIKE '%$g9%' or ss = '$row5[0]' and grado LIKE '%$g10%' or ss = '$row5[0]' and grado LIKE '%$g11%' or ss = '$row5[0]' and grado LIKE '%$g12%'";
   $cursos = DB::table('acumulativa')
   ->whereRaw("ss = '$student->ss' and grado LIKE '%$g1%' or ss = '$student->ss' and grado LIKE '%$g2%' or ss = '$student->ss' and grado LIKE '%$g3%' or ss = '$student->ss' and grado LIKE '%$g4%' or ss = '$student->ss' and grado LIKE '%$g5%' or ss = '$student->ss' and grado LIKE '%$g6%' or ss = '$student->ss' and grado LIKE '%$g7%' or ss = '$student->ss' and grado LIKE '%$g8%' or ss = '$student->ss' and grado LIKE '%$g9%' or ss = '$student->ss' and grado LIKE '%$g10%' or ss = '$student->ss' and grado LIKE '%$g11%' or ss = '$student->ss' and grado LIKE '%$g12%'")->orderBy('apellidos')->get();
         $cc1=1;
         }
      else
         {
         $sSQL2="select * from acumulativa where ss = '$row5[0]' and grado LIKE '%$g1%' or ss = '$row5[0]' and grado LIKE '%$g2%' or ss = '$row5[0]' and grado LIKE '%$g3%' or ss = '$row5[0]' and grado LIKE '%$g4%' or ss = '$row5[0]' and grado LIKE '%$g5%' or ss = '$row5[0]' and grado LIKE '%$g6%' or ss = '$row5[0]' and grado LIKE '%$g7%' or ss = '$row5[0]' and grado LIKE '%$g8%' or ss = '$row5[0]' and grado LIKE '%$g9%' or ss = '$row5[0]' and grado LIKE '%$g10%' or ss = '$row5[0]' and grado LIKE '%$g11%' or ss = '$row5[0]' and grado LIKE '%$g12%'";
   $cursos = DB::table('acumulativa')
   ->whereRaw("ss = '$student->ss' and grado LIKE '%$g1%' or ss = '$student->ss' and grado LIKE '%$g2%' or ss = '$student->ss' and grado LIKE '%$g3%' or ss = '$student->ss' and grado LIKE '%$g4%' or ss = '$student->ss' and grado LIKE '%$g5%' or ss = '$student->ss' and grado LIKE '%$g6%' or ss = '$student->ss' and grado LIKE '%$g7%' or ss = '$student->ss' and grado LIKE '%$g8%' or ss = '$student->ss' and grado LIKE '%$g9%' or ss = '$student->ss' and grado LIKE '%$g10%' or ss = '$student->ss' and grado LIKE '%$g11%' or ss = '$student->ss' and grado LIKE '%$g12%'")->orderBy('apellidos')->get();
         $cc1=2;
         }
      
      $n1=0;$n2=0;$n3=0;$n4=0;$n5=0;$not=0;$tns=0;
      $cr1=0;$cr2=0;$cr3=0;
    foreach ($cursos as $curso) 
            {
            $cc=0;
//            ECHO substr($row2[5], 0, -3).'/'.substr($row2[5], 0, 3);
            if (substr($curso->curso, 0, 3) == $_POST[mat1] and $cc1==2) {
                $cc=3;
               }
            if (substr($curso->curso, 0, 3) == $_POST[mat2] and $cc1==2) {
                $cc=3;
               }
            if (substr($curso->curso, 0, 3) == $_POST[mat3] and $cc1==2) {
                $cc=3;
               }
            if (substr($curso->curso, 0, 3) == $_POST[mat4] and $cc1==2) {
                $cc=3;
               }
            if ($curso->sem1 > 0 and $cc1==1)
               {
               $not=$not+$curso->sem1;$tns=$tns+1;
               $ct=$curso->credito/2;
//               $not=$not+($row2[8]*$ct);$tns=$tns+$ct;
               if ($curso->sem1 > 89){$n1=$n1+1;$cr1=$cr1+(4*$ct);$cr2=$cr2+$ct;}
               else
                  if ($curso->sem1 > 79){$n2=$n2+1;$cr1=$cr1+(3*$ct);$cr2=$cr2+$ct;}
                  else
                     if ($curso->sem1 > 69){$n3=$n3+1;$cr1=$cr1+(2*$ct);$cr2=$cr2+$ct;}
                     else
                        if ($curso->sem1 > 59){$n4=$n4+1;$cr1=$cr1+(1*$ct);$cr2=$cr2+$ct;}
                        else
                           if ($curso->sem1 > 0){$n5=$n5+1;$cr2=$cr2+$ct;}
               }
            if ($curso->sem2 > 0 and $cc1==1)
               {
               $ct=$curso->credito/2;
               $not=$not+$curso->sem2;$tns=$tns+1;
//               $not=$not+($row2[9]*$ct);$tns=$tns+$ct;
               if ($curso->sem2 > 89){$n1=$n1+1;$cr1=$cr1+(4*$ct);$cr2=$cr2+$ct;}
               else
                  if ($curso->sem2 > 79){$n2=$n2+1;$cr1=$cr1+(3*$ct);$cr2=$cr2+$ct;}
                  else
                     if ($curso->sem2 > 69){$n3=$n3+1;$cr1=$cr1+(2*$ct);$cr2=$cr2+$ct;}
                     else
                        if ($curso->sem2 > 59){$n4=$n4+1;$cr1=$cr1+(1*$ct);$cr2=$cr2+$ct;}
                        else
                           if ($curso->sem2 > 0){$n5=$n5+1;$cr2=$cr2+$ct;}
               }
            if ($curso->sem1 > 0 and $cc==3)
               {
               $ct=$curso->credito/2;
               $not=$not+$curso->sem1;$tns=$tns+1;
//               $not=$not+($row2[8]*$ct);$tns=$tns+$ct;
               if ($curso->sem1 > 89){$n1=$n1+1;$cr1=$cr1+(4*$ct);$cr2=$cr2+$ct;}
               else
                  if ($curso->sem1 > 79){$n2=$n2+1;$cr1=$cr1+(3*$ct);$cr2=$cr2+$ct;}
                  else
                     if ($curso->sem1 > 69){$n3=$n3+1;$cr1=$cr1+(2*$ct);$cr2=$cr2+$ct;}
                     else
                        if ($curso->sem1 > 59){$n4=$n4+1;$cr1=$cr1+(1*$ct);$cr2=$cr2+$ct;}
                        else
                           if ($curso->sem1 > 0){$n5=$n5+1;$cr2=$cr2+$ct;}
               }
            if ($curso->sem2 > 0 and $cc==3)
               {
               $ct=$curso->credito/2;
               $not=$not+$curso->sem2;$tns=$tns+1;
//               $not=$not+($row2[9]*$ct);$tns=$tns+$ct;
               if ($curso->sem2 > 89){$n1=$n1+1;$cr1=$cr1+(4*$ct);$cr2=$cr2+$ct;}
               else
                  if ($curso->sem2 > 79){$n2=$n2+1;$cr1=$cr1+(3*$ct);$cr2=$cr2+$ct;}
                  else
                     if ($curso->sem2 > 69){$n3=$n3+1;$cr1=$cr1+(2*$ct);$cr2=$cr2+$ct;}
                     else
                        if ($curso->sem2 > 59){$n4=$n4+1;$cr1=$cr1+(1*$ct);$cr2=$cr2+$ct;}
                        else
                           if ($curso->sem2 > 0){$n5=$n5+1;$cr2=$cr2+$ct;}
               }
            }
      $notas=0;
      $notas=$notas+$n1*4;
      $notas=$notas+$n2*3;
      $notas=$notas+$n3*2;
      $notas=$notas+$n4*1;
//    echo $cr1.' '.$cr2.'/';
//      if ($tns > 0)
      if ($cr2 > 0)
         {
         $sql="UPDATE year SET cn1='$n1',cn2='$n2',cns1='$n3',cn3='$n4',cn4='$n5',fin = $not/$tns, cnf=$tns, cns2=".round($notas/$tns,2)." WHERE ss='$row5[0]' and year='$_POST[year]'";
         $sql="UPDATE year SET cn1='$n1',cn2='$n2',cns1='$n3',cn3='$n4',cn4='$n5',fin = $not/$tns, cnf=$tns, cns2=".round($cr1/$cr2,2)." WHERE ss='$row5[0]' and year='$_POST[year]'";

        $thisCourse2 = DB::table("year")->where([
            ['ss', $student->ss],
            ['year', $year]
        ])->update([
            'cn1' => $n1,
            'cn2' => $n2,
            'cns1' => $n3,
            'cn3' => $n4,
            'cn4' => $n5,
            'fin' => $not/$tns,
            'cnf' => $tns,
            'cns2' => round($cr1/$cr2,2),
        ]);

//         mysql_query($sql);
         }
      }

if ($_POST['gra']==1)
   {
//   $sSQL3="select * from year where year = '$_POST[year]' and grado LIKE '%$_POST[grado]%' and activo = '' and fin > 0 Order By cns2 DESC";
   list($g,$s) = explode("-",$grado);
   $sSQL3="select * from year where year = '$_POST[year]' and grado LIKE '%$_POST[grado]%' and activo = '' and fin > 0 Order By cns2 DESC, fin DESC";
   $sSQL1="select * from year where year = 'year' and grado LIKE '%$g%' and activo = '' Order By apellidos";
   $students = DB::table('year')
   ->whereRaw("year = '$year' and grado like '%".$g."%' and activo = ''" )->orderBy('cns2 DESC, fin DESC')->get();

   }
else
   {
//   $sSQL3="select * from year where year = '$_POST[year]' and grado LIKE '$_POST[grado]' and activo = '' and fin > 0 Order By cns2 DESC";
   $sSQL3="select * from year where year = '$_POST[year]' and grado LIKE '$_POST[grado]' and activo = '' and fin > 0 Order By cns2 DESC, fin DESC";
   $students = DB::table('year')
   ->whereRaw("year = '$year' and grado like '$grado' and activo = ''" )->orderBy('cns2 DESC, fin DESC')->get();
   }
//$result3=mysql_query($sSQL3);
		
$x=1;
$l = $_POST['lineas'];
$ad = $_POST['liad'];
foreach ($students as $student) 
        {
      $notas=0;
      $notas=$notas+$student->cn1*4;
      $notas=$notas+$student->cn2*3;
      $notas=$notas+$student->cn3*2;
      $notas=$notas+$student->cns1*1;
	  $pdf->Cell(7,5,$x,$l,0,'R');
	  $pdf->Cell(68,5,$student->apellidos.' '.$student->nombre,$l,0,'L');
	  $pdf->Cell(10,5,$student->cn1,$l,0,'R');
	  $pdf->Cell(10,5,$student->cn2,$l,0,'R');
	  $pdf->Cell(10,5,$student->cns1,$l,0,'R');
	  $pdf->Cell(10,5,$student->cn3,$l,0,'R');
	  $pdf->Cell(10,5,$student->cn4,$l,0,'R');  	
	  $pdf->Cell(10,5,$student->cnf,$l,0,'R');  	
//	  $pdf->Cell(14,5,$student->cns2,$l,0,'R');  	
	  $pdf->Cell(14,5,number_format($student->cns2, 2),$l,0,'R');  	
//	  $pdf->Cell(14,5,number_format($notas/$row5[37], 2),$l,0,'R');
	  $pdf->Cell(15,5,number_format($student->fin,2),$l,0,'R');
	  if ($_POST['ss']==1)
	     {
         $pdf->Cell(28,5,$student->ss,$l,1,'R');
         }
      else
	     {
         $pdf->Cell(28,5,'',$l,1,'R');
         }
	  $x=$x+1;
 	  }
$pdf->Output();
?>




