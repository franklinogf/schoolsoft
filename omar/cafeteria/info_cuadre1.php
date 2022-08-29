<?php
include('../control.php');
require('../fpdf16/fpdf.php');
$metodo = array('1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'ID', '4' => 'Nombre', '5' => 'ATH');


$dat = "select * from colegio where usuario = 'administrador'";
$tab = mysql_query($dat, $con) or die("problema con query");
$row = mysql_fetch_object($tab);
$year = $row->year;

class PDF extends FPDF
{

	//Cabecera de p&#65533;gina
	function Header()
	{
		include('../control.php');
		$dat = "select * from colegio where usuario = 'administrador'";
		$tab = mysql_query($dat, $con) or die("problema con query");
		$row = mysql_fetch_row($tab);

		//Logo
		$this->Image('../logo/logo.gif', 10, 10, 25);
		//Arial bold 15
		$this->SetFont('Arial', 'B', 15);
		//Movernos a la derecha
		$sp = 120;
		$this->Cell($sp);
		//Ttulo
		$this->Cell(30, 5, $row[0], 0, 1, 'C');
		//	IF($row[52]=='SI'){$this->Cell(80);$this->Cell(30,8,$row[44],0,0,'C');}
		$this->SetFont('Arial', '', 9);
		//Movernos a la derecha
		$this->Ln(2);
		$this->Cell($sp);
		$this->Cell(30, 2, $row[1], 0, 0, 'C');
		$this->Ln(3);
		$this->Cell($sp);
		$this->Cell(30, 2, $row[2], 0, 0, 'C');
		$this->Ln(3);
		$this->Cell($sp);
		$this->Cell(30, 2, $row[3] . ', ' . $row[4] . ' ' . $row[5], 0, 0, 'C');
		$this->Ln(3);
		$this->Cell($sp);
		$this->SetFont('Arial', '', 8);
		$this->Cell(30, 3, 'Tel. ' . $row[12] . ' Fax ' . $row[13], 0, 0, 'C');
		$this->Ln(3);
		$this->Cell($sp);
		$this->Cell(30, 3, $row[20], 0, 0, 'C');
		//Salto de lnea
		$this->Ln(8);
		$this->Cell($sp);
		$this->SetFont('Arial', 'B', 11);
		$this->Cell(30, 10, 'CUADRE DEL DIA DETALLADO', 0, 1, 'C');
		$this->Ln(5);

		$this->SetFillColor(230);
		$this->Cell(0, 5, "Desde {$_POST['fecha1']} Hasta {$_POST['fecha2']}", 0, 1);
		$this->Ln(3);

		$this->Cell(15, 5, 'ID', 1, 0, 'C', true);
		$this->Cell(70, 5, utf8_decode('Artículo'), 1, 0, 'C', true);
		$this->Cell(25, 5, 'Fecha', 1, 0, 'C', true);
		$this->Cell(30, 5, 'TDP', 1, 0, 'C', true);
		$this->Cell(25, 5, 'Precio', 1, 0, 'C', true);
		$this->Cell(25, 5, 'ID Est.', 1, 0, 'C', true);
		$this->Cell(70, 5, 'Nombre Estudiante', 1, 1, 'C', true);
	}

	//Pie de p&#65533;gina
	/*function Footer()
{

    //Posici&oacute;n: a 1,5 cm del final
    $this->SetY(-15);

    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //N&uacute;mero de p&aacute;gina
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}'.' / '.date('m-d-Y'),0,0,'C');

}*/

	function foo($bb)
	{
		$this->SetFont('Arial', 'B', 11);
		$this->Cell(80);
		$this->Cell(30, 5, $bb, 0, 1, 'C');
		$this->SetFont('Arial', '', 11);
		$this->Cell(2, 5, '', 0, 0, 'C');
	}
}

$result = mysql_query("SELECT * FROM compra_cafeteria WHERE year = '$year' and (fecha >= '{$_POST['fecha1']}' and fecha <= '{$_POST['fecha2']}') ORDER BY fecha");

//Creaci&#65533;n del objeto de la clase heredada
$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 5);
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(230);

//$pdf->Cell(0,5,"Desde {$_POST['fecha1']} Hasta {$_POST['fecha2']}",0,1);
//$pdf->Ln(3);

//$pdf->Cell(15,5,'ID',1,0,'C',true);
//$pdf->Cell(70,5,'Art�culo',1,0,'C',true);
//$pdf->Cell(25,5,'Fecha',1,0,'C',true);
//$pdf->Cell(30,5,'TDP',1,0,'C',true);
//$pdf->Cell(25,5,'Precio',1,0,'C',true);
//$pdf->Cell(25,5,'ID Est.',1,1,'C',true);




$pdf->SetFont('Times', '', 12);
$precio = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0);
// $tdp = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0);
$TOTAL = 0;
while ($compras = mysql_fetch_object($result)) {

	$re = mysql_query("SELECT * FROM compra_cafeteria_detalle WHERE id_compra = '$compras->id'");

	while ($articulo = mysql_fetch_object($re)) {
		$price = $articulo->precio_final ? $articulo->precio_final : $articulo->precio;
		$pdf->Cell(15, 5, $compras->id, 0, 0, 'C');
		$pdf->Cell(70, 5, $articulo->descripcion, 0);
		$pdf->Cell(25, 5, $compras->fecha, 0, 0, 'C');
		$pdf->Cell(30, 5, $metodo[$compras->tdp], 0, 0, 'C');
		$pdf->Cell(25, 5, $price, 0, 0, 'C');
		$pdf->Cell(25, 5, $compras->ss, 0, 0, 'C');
		$pdf->Cell(70, 5, $compras->apellido . ' ' . $compras->nombre, 0, 1, 'L');

		$precio[$compras->tdp] += $price;
		$TOTAL += number_format($price, 2);
	}
}



$pdf->Ln(5);

$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Ln(2);
foreach ($metodo as $met => $key) {
	if ($precio[$met] > 0) {
		$pdf->Cell(30, 5, $key, 0, 0);
		$pdf->Cell(20);
		$pdf->Cell(30, 5, '$' . number_format($precio[$met], 2), 0, 1);
	}
}
$pdf->SetFont('Times', 'B', 12);

$pdf->Cell(50);
$pdf->Cell(30, 3, '', 'B', 1);
$pdf->Cell(30, 5, 'Total', 0, 0);
$pdf->Cell(20);
$pdf->Cell(30, 5, '$' . number_format($TOTAL, 2), 0, 1);
$pdf->Cell(80, 1, '', 'B', 2);
$pdf->Cell(80, 1, '', 'B', 2);






$pdf->Output();
