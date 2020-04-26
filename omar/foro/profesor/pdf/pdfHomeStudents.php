<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\Controllers\Teacher;


Session::is_logged();

$teacher = new Teacher(Session::id());
$students = $teacher->homeStudents();

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle('Salon hogar');
$pdf->Fill();


$pdf->SetFont('Arial', 'B', 12);
$pdf->splitCells("Salon Hogar: $teacher->grado", $teacher->fullName());
// table header
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(10,7,"","LTB",0,"C",true);	
$pdf->Cell(15,7,"ID","RTB",0,"C",true);	
$pdf->Cell(50,7,"Apellidos",1,0,"C",true);
$pdf->Cell(40,7,"Nombre",1,0,"C",true);			
$pdf->Cell(75,7,"Firma",1,1,"C",true);

$pdf->SetFont('Arial', '', 10);

$num = 1;
foreach ($students as $student) {

	$pdf->Cell(10, 5, $num, 0, 0, "R");
	$pdf->Cell(15, 5, $student->id, 0, 0, "C");
	$pdf->Cell(50, 5, ucwords(utf8_decode($student->apellidos)));
	$pdf->Cell(40, 5, ucwords(utf8_decode($student->nombre)));
	$pdf->Cell(75,5,"","B",1,"C");	
	$num++;
}
			
$pdf->Output();
