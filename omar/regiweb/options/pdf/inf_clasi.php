<?
session_start();
$id=$_SESSION['id1'];
$usua=$_SESSION['usua1'];
if ($usua == ""){
  exit;
}
require('../fpdf16/fpdf.php');

class PDF extends FPDF
{
//<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">

//Cabecera de pgina
function Header()
{
    include('../control.php');
    $dat = "select * from colegio where usuario = 'administrador'";
    $tab = mysql_query($dat, $con) or die ("problema con query") ;
    $row = mysql_fetch_row($tab);
    

	//Logo
	$this->Image('../logo/logo.gif',10,10,25);
	//Arial bold 15
	$this->SetFont('Arial','B',15);
	//Movernos a la derecha
	$this->Cell(80);
	//Ttulo
	$this->Cell(30,5,$row[0],0,1,'C');
	IF($row[52]=='SI'){$this->Cell(80);$this->Cell(30,8,$row[44],0,0,'C'); $this->Ln(8);}ELSE{$this->Ln(5);}
	$this->SetFont('Arial','',9);
	//Movernos a la derecha
	$this->Cell(80);
	$this->Cell(30,2,$row[1],0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,2,$row[2],0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->SetFont('Arial','',8);
	$this->Cell(30,3,'Tel. '.$row[12].' Fax '.$row[13],0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,3,$row[20],0,0,'C');
	//Salto de lnea
	$this->Ln(10);
	$this->Cell(80);

}


}

//Creacin del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','B',11);
include('../control.php');
$aa = "ALTER TABLE `year` CHANGE `cnf` `cnf` CHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";
mysql_query($aa);

$consult1 = "select * from colegio where usuario = 'administrador'";
$resultad1 = mysql_query($consult1);
$row2=mysql_fetch_array($resultad1);
$resultado = mysql_query("SELECT grado FROM profesor WHERE id = '$id'");
$profe = mysql_fetch_object($resultado);
$grado = $profe->grado;

   $q = "select * from year where grado='".$grado."' AND year='$row2[116]' ORDER BY grado, apellidos, nombre";
   
$tabla1 = mysql_query($q, $dbh) or die ("problema con query") ;
$reg = mysql_fetch_row($tabla1);
$result=mysql_query($q);
	 $pdf->SetFont('Arial','B',12);
 	 $pdf->Cell(30,5,'Informe de Clasificación de Notas ',0,1,'C');
	 $pdf->Cell(80);
 	 $pdf->Cell(30,5,'GRADO '.$grado.' / AÑO '.$row2[116],0,0,'C');
     $pdf->Cell(8,10,'',0,1,'C');

     $pdf->SetFillColor(240);
     $pdf->Cell(80,5,'NOMBRE ESTUDIANTES',1,0,'C',true);
     $pdf->Cell(8,5,'A-1',1,0,'C',true);
     $pdf->Cell(8,5,'B-1',1,0,'C',true);
     $pdf->Cell(8,5,'C-1',1,0,'C',true);
     $pdf->Cell(8,5,'A-2',1,0,'C',true);
     $pdf->Cell(8,5,'B-2',1,0,'C',true);
     $pdf->Cell(8,5,'C-2',1,0,'C',true);
     $pdf->Cell(8,5,'A-3',1,0,'C',true);
     $pdf->Cell(8,5,'B-3',1,0,'C',true);
     $pdf->Cell(8,5,'C-3',1,0,'C',true);
     $pdf->Cell(8,5,'A-4',1,0,'C',true);
     $pdf->Cell(8,5,'B-4',1,0,'C',true);
     $pdf->Cell(8,5,'C-4',1,0,'C',true);
     $pdf->Cell(15,5,'PROM',1,1,'C',true);

while ($row1=mysql_fetch_array($result))
      {

$a=0;
$gra='';
     $pdf->SetFont('Times','',11);

$q2 = "select * from padres where ss='$row1[0]' AND year='$row2[116]' ORDER BY curso";
$tabla2 = mysql_query($q2, $dbh) or die ("problema con query") ;
$reg2 = mysql_fetch_row($tabla2);
$result2=mysql_query($q2);
$a1=0;$a2=0;$a3=0;$a4=0;
$b1=0;$b2=0;$b3=0;$b4=0;
$c1=0;$c2=0;$c3=0;$c4=0;
$d1=0;$d2=0;$d3=0;$d4=0;
$f1=0;$f2=0;$f3=0;$f4=0;
$tf=0;$tc=0;
$tgf=0;$tgc=0;
while ($row=mysql_fetch_array($result2))
      {
      $tf=0;$tc=0;
      if ($row[19]>89){$a1=$a1+1;$tf=$tf+$row[19];$tc=$tc+1;}
      else
         if ($row[19]>79){$b1=$b1+1;$tf=$tf+$row[19];$tc=$tc+1;}
         else
            if ($row[19]>69){$c1=$c1+1;$tf=$tf+$row[19];$tc=$tc+1;}
            else
               if ($row[19]>59){$d1=$d1+1;$tf=$tf+$row[19];$tc=$tc+1;}
               else
                  if ($row[19]>0){$f1=$f1+1;$tf=$tf+$row[19];$tc=$tc+1;}
      
      if ($row[20]>89){$a2=$a2+1;$tf=$tf+$row[20];$tc=$tc+1;}
      else
         if ($row[20]>79){$b2=$b2+1;$tf=$tf+$row[20];$tc=$tc+1;}
         else
            if ($row[20]>69){$c2=$c2+1;$tf=$tf+$row[20];$tc=$tc+1;}
            else
               if ($row[20]>59){$d2=$d2+1;$tf=$tf+$row[20];$tc=$tc+1;}
               else
                  if ($row[20]>0){$f2=$f2+1;$tf=$tf+$row[20];$tc=$tc+1;}

//      if ($row[13]>89){$a3=$a3+1;$tf=$tf+$row[13];$tc=$tc+1;}
//      else
//         if ($row[13]>79){$b3=$b3+1;$tf=$tf+$row[13];$tc=$tc+1;}
//         else
//            if ($row[13]>69){$c3=$c3+1;$tf=$tf+$row[13];$tc=$tc+1;}
//            else
//               if ($row[13]>59){$d3=$d3+1;$tf=$tf+$row[13];$tc=$tc+1;}
//               else
//                  if ($row[13]>0){$f3=$f3+1;$tf=$tf+$row[13];$tc=$tc+1;}

//      if ($row[14]>89){$a4=$a4+1;$tf=$tf+$row[14];$tc=$tc+1;}
//      else
//         if ($row[14]>79){$b4=$b4+1;$tf=$tf+$row[14];$tc=$tc+1;}
//         else
//            if ($row[14]>69){$c4=$c4+1;$tf=$tf+$row[14];$tc=$tc+1;}
//            else
//               if ($row[14]>59){$d4=$d4+1;$tf=$tf+$row[14];$tc=$tc+1;}
//               else
//                  if ($row[14]>0){$f4=$f4+1;$tf=$tf+$row[14];$tc=$tc+1;}
      if ($tc > 0)
         {
         $tgf=$tgf+round($tf/$tc,0);$tgc=$tgc+1;
         }
      }
     $GTF='';
     $cl=$a1.'-'.$a2.'-'.$a3.'-'.$a4.'-'.$b1.'-'.$b2.'-'.$b3.'-'.$b4.'-'.$c1.'-'.$c2.'-'.$c3.'-'.$c4;
     if($tc > 0){$GTF=round($tgf/$tgc,2);}
$q = "update year set fin='$GTF',cnf='$cl' where ss='$row1[0]' and year='$row2[116]'";

mysql_query($q) or die ("problema con query");


}

$q = "select * from year where grado='".$grado."' AND year='$row2[116]' ORDER BY grado, fin DESC";
$tabla1 = mysql_query($q, $dbh) or die ("problema con query") ;
$result=mysql_query($q);
while ($row1=mysql_fetch_array($result))
      {
     list($a1, $a2, $a3, $a4, $b1, $b2, $b3, $b4, $c1, $c2, $c3, $c4) = explode("-",$row1[37]);

$a=0;
$gra='';

     $x=$x+1;
     $pdf->SetFont('Times','',11);
     $pdf->Cell(5,5,$x,1,0,'R');
     $pdf->Cell(75,5,$row1[4].' '.$row1[3],1,0);
     $pdf->Cell(8,5,$a1,1,0,'R');
     $pdf->Cell(8,5,$b1,1,0,'R');
     $pdf->Cell(8,5,$c1,1,0,'R');
     $pdf->Cell(8,5,$a2,1,0,'R');
     $pdf->Cell(8,5,$b2,1,0,'R');
     $pdf->Cell(8,5,$c2,1,0,'R');
     $pdf->Cell(8,5,$a3,1,0,'R');
     $pdf->Cell(8,5,$b3,1,0,'R');
     $pdf->Cell(8,5,$c3,1,0,'R');
     $pdf->Cell(8,5,$a4,1,0,'R');
     $pdf->Cell(8,5,$b4,1,0,'R');
     $pdf->Cell(8,5,$c4,1,0,'R');
     $pdf->Cell(15,5,$row1[29],1,1,'R');
     }



$pdf->Output();
?>