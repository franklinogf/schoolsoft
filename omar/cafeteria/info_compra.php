<?php
include('../control.php');
// require('../fpdf16/fpdf.php');
require('../fpdf16/pdf_codabar.php');
$metodo = array('1' => 'Efectivo','2'=>'Tarjeta','3'=>'ID','4'=>'Nombre','5'=>'ATH');

$result = mysql_query("SELECT * FROM inventario");
$dat = "select * from colegio where usuario = 'administrador'";
$tab = mysql_query($dat, $con) or die ("problema con query") ;
$row = mysql_fetch_object($tab);
$year = $row->year;

class PDF extends PDF_Codabar
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
    $this->Cell(30,10,'INFORME DE COMPRAS',0,1,'C');
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
$pdf->SetLeftMargin(5);
$pdf->SetFont('Times','B',12);
$pdf->SetFillColor(230);

$pdf->Ln(3);

$pdf->Cell(20,5,'ID',1,0,'C',true);
$pdf->Cell(70,5,'Artículo',1,0,'C',true);
$pdf->Cell(20,5,'Precio',1,0,'C',true);
$pdf->Cell(20,5,'Cantidad',1,0,'C',true);
$pdf->Cell(15,5,'Minimo',1,0,'C',true);
$pdf->Cell(40,5,'Codigo de barra',1,0,'C',true);
$pdf->Cell(17,5,'Comprar',1,1,'C',true);




$pdf->SetFillColor(0);

while ($articulo = mysql_fetch_object($result)) {	
$pdf->SetFont('Times','',12); 
		
	if ($articulo->cantidad <= $articulo->minimo) {
		$pdf->Cell(20,10,$articulo->id2,1,0,'C');
		$pdf->Cell(70,10,$articulo->articulo,1);
		$pdf->Cell(20,10,$articulo->precio,1,0,'R');
		$pdf->Cell(20,10,$articulo->cantidad,1,0,'C');			
		$pdf->Cell(15,10,$articulo->minimo,1,0,'C');
		$pdf->Codabar($pdf->GetX(),$pdf->GetY(),$articulo->cbarra,'*','*',0.195,5);			
		$pdf->Cell(40,10,'',1);		
		$pdf->Cell(17,10,'',1,1);		
	}

}








  
$pdf->Output();







