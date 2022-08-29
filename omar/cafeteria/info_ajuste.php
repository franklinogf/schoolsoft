<?php
include('../control.php');
require('../fpdf16/fpdf.php');
$metodo = array('1' => 'Efectivo','2'=>'Tarjeta','3'=>'ID','4'=>'Nombre','5'=>'ATH');

$result = mysql_query("SELECT * FROM inventario");
$dat = "select * from colegio where usuario = 'administrador'";
$tab = mysql_query($dat, $con) or die ("problema con query") ;
$row = mysql_fetch_object($tab);
$year = $row->year;

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
    $this->Cell(30,10,'AJUSTE DE INVENTARIO',0,1,'C');
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
$pdf->SetFillColor(230);

$pdf->Cell(0,5,"Desde {$_POST['fecha1']} Hasta {$_POST['fecha2']}",0,1);
$pdf->Ln(3);

$pdf->Cell(20,5,'ID',1,0,'C',true);
$pdf->Cell(70,5,'Artículo',1,0,'C',true);
$pdf->Cell(30,5,'Fecha',1,0,'C',true);
$pdf->Cell(30,5,'Tipo',1,0,'C',true);
$pdf->Cell(30,5,'Precio',1,1,'C',true);




$pdf->SetFont('Times','',12); 
$precio = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0);
// $tdp = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0);
while ($articulo = mysql_fetch_object($result)) {

	$re = mysql_query("SELECT d.id_inv,c.fecha,c.tdp,c.year,d.precio FROM compra_cafeteria_detalle as d  LEFT JOIN compra_cafeteria as c on d.id_compra = c.id WHERE d.id_inv = '$articulo->id2' and c.year ='$year' and (c.fecha >= '{$_POST['fecha1']}' and c.fecha <= '{$_POST['fecha2']}')");
	$cant = 0;
	if (mysql_num_rows($re) > 0) {	
	// $cant = mysql_num_rows($re);
		while ($comp = mysql_fetch_object($re)) {			
		
			$pdf->Cell(20,5,$articulo->id2,0,0,'C');
			$pdf->Cell(70,5,$articulo->articulo,0);
			$pdf->Cell(30,5,$comp->fecha,0,0,'C');
			$pdf->Cell(30,5,$metodo[$comp->tdp],0,0,'C');			
			$pdf->Cell(30,5,$comp->precio,0,1,'C');			

			$precio[$comp->tdp]+=$comp->precio;
		}
		
	}
}



$pdf->Ln(5);
$TOTAL = 0;
$pdf->Cell(80,1,'','B',2);
$pdf->Cell(80,1,'','B',2);
$pdf->Ln(2);
foreach ($metodo as $met => $key) {
	if ($precio[$met]>0) {
		$pdf->Cell(30,5,$key,0,0);
		$pdf->Cell(20);
		$pdf->Cell(30,5,'$'.number_format($precio[$met],2),0,1);
		$TOTAL+=number_format($precio[$met],2);
	}
	
}
$pdf->SetFont('Times','B',12); 

$pdf->Cell(50);
$pdf->Cell(30,3,'','B',1);
$pdf->Cell(30,5,'Total',0,0);
$pdf->Cell(20);
$pdf->Cell(30,5,'$'.number_format($TOTAL,2),0,1);
$pdf->Cell(80,1,'','B',2);
$pdf->Cell(80,1,'','B',2);





  
$pdf->Output();







