<?php
require '../session.php';
$ID = $_SESSION['id'];
$re = mysql_query("SELECT year FROM colegio WHERE usuario='administrador'",$con);
$re = mysql_fetch_assoc($re);
$year = $re['year'];
$re = mysql_query("SELECT grado,nombre,apellidos FROM profesor WHERE id='$ID'",$con);
$re = mysql_fetch_assoc($re);
$grado = $re['grado'];
$nombreProfesor = $re['nombre']." ".$re["apellidos"];
$result = mysql_query("SELECT * FROM year WHERE grado='$grado' AND year='$year' and fecha_baja='0000-00-00' ORDER BY apellidos",$con);

require('../../../fpdf16/fpdf.php');

class PDF extends FPDF
{
	 public $profesor = '';
	public $grado = '';
	 function setProfesor($profesor){
	 	$this->profesor = $profesor;
	 }
	 function setGrado($grado){
	 	$this->grado = $grado;
	 }
//Cabecera de pagina
	
	function Header()
	{	
		require('../../control.php');
	   
		
	    $tab = mysql_query("SELECT * FROM colegio WHERE usuario = 'administrador'", $con);	   
		$colegio = mysql_fetch_object($tab);
		$year = $colegio->year;
		//informacion del colegio
//		if($colegio->logo != "NO") $this->Image('../../logo/'.$colegio->logo,10,10,25);
	    $this->Image('../../logo/logo.gif',10,10,25);
		$this->SetFont('Arial','B',15);	
		$this->Cell(0,5,$colegio->colegio,0,1,'C');
		$this->SetFont('Arial','',9);	
		$this->Cell(0,3,$colegio->dir1,0,1,'C');	
		$this->Cell(0,5,$colegio->dir2,0,1,'C');	
		$this->Cell(0,3,$colegio->pueblo1.', '.$colegio->esta1.' '.$colegio->zip1,0,1,'C');	
		$this->SetFont('Arial','',8);
		$this->Cell(0,3,'Tel. '.$colegio->telefono.' Fax '.$colegio->fax,0,1,'C');
		$this->Cell(0,3,$colegio->correo,0,1,'C');
		$this->Ln(10);			  	
		$this->SetFont('Arial','',9);
		$this->Cell(92.5,5,$this->grado,0,0,"L");
		$this->Cell(92.5,5,$this->profesor,0,1,"R");
		//columnas
		$this->SetFont('Arial','B',13);
		$this->SetFillColor(195,217,255);				
		$this->Cell(10,7,"","LTB",0,"C",true);	
		$this->Cell(15,7,"ID","RTB",0,"C",true);	
		$this->Cell(60,7,"Apellido",1,0,"C",true);
		$this->Cell(40,7,"Nombre",1,0,"C",true);			
		$this->Cell(35,7,"Usuario",1,0,"C",true);							
		$this->Cell(35,7,"Clave",1,0,"C",true);							
		$this->Ln();	


	}
	function Footer()
	{
		$idioma = $_SESSION['idioma'];
		if ($idioma == "Es") {
			$footer = 'Pagina '.$this->PageNo().' de {nb} '.' | '.date("m-d-Y");
		}else{
			$footer = 'Page '.$this->PageNo().' of {nb} '.' | '.date("m-d-Y");
		}
	    $this->SetY(-15);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Numero de pagina
	    $this->Cell(0,10,$footer,0,0,'C');

	}
 
}

$pdf = new PDF();
$pdf->AliasNbPages();
// $pdf->SetAutoPageBreak(true);
$pdf->setGrado("Salon hogar: $grado");	
$pdf->setProfesor($nombreProfesor);
$pdf->AddPage();	
$num = 1;
$pdf->SetFont('Arial','',10);
while ($estudiante = mysql_fetch_object($result)) {	
	
	$pdf->SetFillColor(229, 236, 249);				
	$pdf->Cell(10,5,$num,1,0,"R");	
	$pdf->Cell(15,5,$estudiante->id,1,0,"C");	
	$pdf->Cell(60,5,ucwords(utf8_decode($estudiante->apellidos)),1);
	$pdf->Cell(40,5,ucwords(utf8_decode($estudiante->nombre)),1);		
	$pdf->Cell(35,5,$estudiante->usuario,1,0,"C");				
	$pdf->Cell(35,5,$estudiante->clave,1,0,"C");				
	$pdf->Ln();	
	$num++;
}

$pdf->Output();

?>
