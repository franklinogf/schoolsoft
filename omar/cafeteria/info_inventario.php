<?php
require('../fpdf16/fpdf.php');

class PDF extends FPDF
{

//Cabecera de p&#65533;gina
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
	$sp=80;
	$this->Cell($sp);
	//Ttulo
	$this->Cell(30,5,$row[0],0,1,'C');
	IF($row[52]=='SI'){$this->Cell(80);$this->Cell(30,8,$row[44],0,0,'C');}
	$this->SetFont('Arial','',9);
	//Movernos a la derecha
	$this->Ln(2);
	$this->Cell($sp);
	$this->Cell(30,2,$row[1],0,0,'C');
	$this->Ln(3);
	$this->Cell($sp);
	$this->Cell(30,2,$row[2],0,0,'C');
	$this->Ln(3);
	$this->Cell($sp);
	$this->Cell(30,2,$row[3].', '.$row[4].' '.$row[5],0,0,'C');
	$this->Ln(3);
	$this->Cell($sp);
	$this->SetFont('Arial','',8);
	$this->Cell(30,3,'Tel. '.$row[12].' Fax '.$row[13],0,0,'C');
	$this->Ln(3);
	$this->Cell($sp);
	$this->Cell(30,3,$row[20],0,0,'C');
	//Salto de lnea
	$this->Ln(10);
	$this->Cell($sp);
	$this->SetFont('Arial','B',11);
    $this->Cell(30,10,'INFORME DE INVENTARIO',0,1,'C');
	$this->Ln(8);
    
    $this->SetFillColor(230);    

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

function foo($bb)
{
	$this->SetFont('Arial','B',11);
	$this->Cell(80);
	$this->Cell(30,5,$bb,0,1,'C');
	$this->SetFont('Arial','',11);
	$this->Cell(2,5,'',0,0,'C');


}

}

//Creaci&#65533;n del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();


$pdf->SetFont('Times','B',12);


// ejemplo de color para fondo 
// color seleccionado
$pdf->SetFillColor(230);

// true = al color del fondo
$pdf->Cell(10,5,' ',0,0);
$pdf->Cell(10,5,' ',1,0,'C',true);
$pdf->Cell(30,5,'ID',1,0,'C',true);
$pdf->Cell(70,5,'Artículo',1,0,'C',true);
$pdf->Cell(22,5,'Precio',1,0,'C',true);
$pdf->Cell(22,5,'Cantidad',1,0,'C',true);
$pdf->Cell(22,5,'Minimo',1,1,'C',true);

$pdf->SetFont('Times','',12); 

$x = 0;

include('../control.php');
$consult1 = "select * from colegio where usuario = 'administrador'";
$resultad1 = mysql_query($consult1);
$row=mysql_fetch_array($resultad1);

$q7 = "select * from inventario order by articulo";
$tabla17 = mysql_query($q7, $dbh) or die ("problema con query 22");
$result7=mysql_query($q7);
while ($row7=mysql_fetch_array($result7))
  {
$x++;
 
$pdf->Cell(10,5,' ',0,0);  
$pdf->Cell(10,5,$x,0,0,'R');
$pdf->Cell(30,5,$row7['id'],0,0,'C');
$pdf->Cell(70,5,$row7['articulo'],0,0,'L');
$pdf->Cell(22,5,number_format($row7['precio'],2),0,0,'R');
$pdf->Cell(22,5,$row7['cantidad'],0,0,'C');
$pdf->Cell(22,5,$row7['minimo'],0,1,'C');
  }
  
$pdf->Output();

?>





