<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Student;
use App\Models\Teacher;
use Classes\PDF;
use Classes\Session;

$isCosey = school_config('app.cosey', false);

Session::is_logged();

$teacher = Teacher::query()->with('homeStudents')->findOrFail(Session::id());

$students = !$isCosey ? $teacher->homeStudents : Student::all();

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle('Lista de usuarios');
$pdf->Fill();


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, "Lista de usuarios", 0, 1, 'C');
$pdf->Ln(3);
if (!$isCosey) {
	$pdf->splitCells("Salon Hogar: $teacher->grado", $teacher->fullName);
} else {
	$pdf->Cell(0, 5, $teacher->fullName, 0, 1);
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
	$pdf->Cell(15, 5, $student->id, 1, 0, "C");
	$pdf->Cell(60, 5, $student->apellidos, 1);
	$pdf->Cell(40, 5, $student->nombre, 1);
	$pdf->Cell(35, 5, $student->usuario, 1, 0, "C");
	$pdf->Cell(35, 5, $student->clave, 1, 0, "C");
	$pdf->Ln();
	$num++;
}

$pdf->Output();
