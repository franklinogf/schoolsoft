<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());



$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle('Lista de usuarios');
$pdf->Fill();


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, "Lista de usuarios", 0, 1, 'C');
$pdf->Ln(3);
if (!__COSEY) {
	$students = DB::table('year')
		->where([
			['grado', $teacher->grado],
			['year', $teacher->info('year')],
			['fecha_baja', '0000-00-00']
		])->orderBy('apellidos')->get();
	$pdf->splitCells("Salon Hogar: $teacher->grado", $teacher->fullName());
} else {
	$students = DB::table('year')
		->where([
			['year', $teacher->info('year')],
			['fecha_baja', '0000-00-00']
		])->orderBy('apellidos')->get();
	$pdf->Cell(0, 5, $teacher->fullName(), 0, 1);
}
// table header
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
$pdf->Cell(15, 7, "ID", "RTB", 0, "C", true);
$pdf->Cell(60, 7, "Apellidos", 1, 0, "C", true);
$pdf->Cell(40, 7, "Nombre", 1, 0, "C", true);
$pdf->Cell(35, 7, "Usuario", 1, 0, "C", true);
$pdf->Cell(35, 7, "Clave", 1, 1, "C", true);

$pdf->SetFont('Arial', '', 10);

$num = 1;
foreach ($students as $student) {

	$pdf->Cell(10, 5, $num, 1, 0, "R");
	$pdf->Cell(15, 5, (__COSEY) ? $student->mt : $student->id, 1, 0, "C");
	$pdf->Cell(60, 5, ucwords(utf8_decode($student->apellidos)), 1);
	$pdf->Cell(40, 5, ucwords(utf8_decode($student->nombre)), 1);
	$pdf->Cell(35, 5, $student->usuario, 1, 0, "C");
	$pdf->Cell(35, 5, $student->clave, 1, 0, "C");
	$pdf->Ln();
	$num++;
}

$pdf->Output();
