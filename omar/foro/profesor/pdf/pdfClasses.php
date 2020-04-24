<?php  
require '../session.php';
$ID = $_SESSION['id'];
$re = mysql_query("SELECT year FROM colegio WHERE usuario='administrador'",$con);
$re = mysql_fetch_assoc($re);
$year = $re['year'];
$cursos = $_POST['imprimir'];
require('../../../fpdf16/fpdf.php');

class PDF 
{
	
	public $profesor = '';
	public $curso = '';
	function setProfesor($profesor){
	 	$this->profesor = $profesor;
	 }
	function setCurso($curso){
	 	$this->curso = $curso;
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
		$this->Cell(95,5,$this->curso,0);
		$this->Cell(95,5,$this->profesor,0,1,"R");
		//columnas
		$this->SetFont('Arial','B',13);
		$this->SetFillColor(195,217,255);				
		$this->Cell(10,7,"","LTB",0,"C",true);	
		$this->Cell(15,7,"ID","RTB",0,"C",true);	
		$this->Cell(50,7,"Apellido",1,0,"C",true);
		$this->Cell(40,7,"Nombre",1,0,"C",true);			
		$this->Cell(75,7,"Firma",1,0,"C",true);							
		$this->Ln();	


	}
	function Footer()
	{
		
		$footer = 'Pagina '.$this->PageNo().' de {nb} '.' | '.date("m-d-Y");		
	    $this->SetY(-15);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Numero de pagina
	    $this->Cell(0,10,$footer,0,0,'C');

	}
 
}

$pdf = new PDF();
$pdf->AliasNbPages();
foreach ($cursos as $curso) {
	$num = 1;
	$re = mysql_query("SELECT p.nombre,p.apellidos,c.desc1 FROM profesor as p
	INNER JOIN cursos as c on c.id = p.id WHERE  c.year='$year' AND c.curso='$curso'",$con);
	$re = mysql_fetch_object($re);	
	$nombreProfesor = $re->nombre." ".$re->apellidos;
	$pdf->setCurso(utf8_decode("Curso $curso - $re->desc1"));	
	$pdf->setProfesor($nombreProfesor);
	$pdf->AddPage();	
	$result = mysql_query("SELECT e.nombre,e.apellidos,e.id FROM padres as p 
	INNER JOIN year AS e ON p.ss = e.ss WHERE e.year='$year' AND p.curso='$curso' AND p.year='$year' AND p.id ='$ID' and baja='' ORDER BY e.apellidos",$con);
	while ($estudiante = mysql_fetch_object($result)) {		
		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(229, 236, 249);		
		$pdf->Cell(10,5,$num,0,0,"R");	
		$pdf->Cell(15,5,$estudiante->id,0,0,"C");	
		$pdf->Cell(50,5,ucwords(utf8_decode($estudiante->apellidos)));
		$pdf->Cell(40,5,ucwords(utf8_decode($estudiante->nombre)));		
		$pdf->Cell(75,5,"","B",0,"C");				
		$pdf->Ln();	
		$num++;
	}
		
}

$pdf->Output();

?>
