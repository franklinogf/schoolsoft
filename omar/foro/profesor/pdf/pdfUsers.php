<?php
require_once '../../../app.php';

use Classes\Controllers\Teacher;
use Classes\PDF;
use Classes\Server;
use Classes\Session; 

Server::is_post();

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle('Lista de clases');
$pdf->SetFont('Arial');

$teacher = new Teacher(Session::id());

$grado = $teacher->grado;
$pdf->Cell(0,5,$grado);
$nombreProfesor = $teacher->nombre." ".$teacher->apellidos;
// $result = \mysql_query("SELECT * FROM year WHERE grado='$grado' AND year='$year' and fecha_baja='0000-00-00' ORDER BY apellidos",$con);

// // $pdf->SetAutoPageBreak(true);
// $pdf->setGrado("Salon hogar: $grado");	
// $pdf->setProfesor($nombreProfesor);
// $pdf->AddPage();	
// $num = 1;
// $pdf->SetFont('Arial','',10);
// while ($estudiante = mysql_fetch_object($result)) {	
	
// 	$pdf->SetFillColor(229, 236, 249);				
// 	$pdf->Cell(10,5,$num,1,0,"R");	
// 	$pdf->Cell(15,5,$estudiante->id,1,0,"C");	
// 	$pdf->Cell(60,5,ucwords(utf8_decode($estudiante->apellidos)),1);
// 	$pdf->Cell(40,5,ucwords(utf8_decode($estudiante->nombre)),1);		
// 	$pdf->Cell(35,5,$estudiante->usuario,1,0,"C");				
// 	$pdf->Cell(35,5,$estudiante->clave,1,0,"C");				
// 	$pdf->Ln();	
// 	$num++;
// }

$pdf->Output();
