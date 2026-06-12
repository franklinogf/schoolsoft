<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Classes\PDF;
use Classes\Server;
use Classes\Session;

Session::is_logged();

Server::is_post();

$classes = $_POST['class'];

$subjects = Subject::whereIn('curso', $classes)->get();

$pdf = new PDF();
$pdf->SetTitle('Lista de estudiantes');
$pdf->Fill();

$teacher = Teacher::findOrFail(Session::id());

foreach ($subjects as $class) {

	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->splitCells($class->display_label, $teacher->fullName);
	// table header
	$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
	$pdf->Cell(15, 7, "ID", "RTB", 0, "C", true);
	$pdf->Cell(50, 7, "Apellidos", 1, 0, "C", true);
	$pdf->Cell(40, 7, "Nombre", 1, 0, "C", true);
	$pdf->Cell(75, 7, "Firma", 1, 1, "C", true);


	$students = Student::byClass($class->curso)->get();

	$num = 1;
	$pdf->SetFont('Arial', '', 10);
	foreach ($students as $student) {
		$pdf->Cell(10, 5, $num, 0, 0, "R");
		$pdf->Cell(15, 5, $student->id, 0, 0, "C");
		$pdf->Cell(50, 5, $student->apellidos);
		$pdf->Cell(40, 5, $student->nombre);
		$pdf->Cell(75, 5, "", "B", 0, "C");
		$pdf->Ln();
		$num++;
	}
}

$pdf->Output();
