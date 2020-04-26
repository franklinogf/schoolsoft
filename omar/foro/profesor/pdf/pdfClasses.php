<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\PDF;
use Classes\Server;
use Classes\Session;

Session::is_logged();

Server::is_post();

$classes = $_POST['class'];

$pdf = new PDF();
$pdf->SetTitle('Lista de estudiantes');
$pdf->Fill();

$teacher = new Teacher(Session::id());

foreach ($classes as $class) {
	$year = $teacher->info('year');
	$thisClass = DB::table('cursos')
		->where([
			['year', $year],
			['curso', $class]
		])->first();

	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->splitCells("$class - $thisClass->desc1", $teacher->fullName());
	// table header
	$pdf->Cell(10, 7, "", "LTB", 0, "C", true);
	$pdf->Cell(15, 7, "ID", "RTB", 0, "C", true);
	$pdf->Cell(50, 7, "Apellidos", 1, 0, "C", true);
	$pdf->Cell(40, 7, "Nombre", 1, 0, "C", true);
	$pdf->Cell(75, 7, "Firma", 1, 1, "C", true);

	$students = new Student();
	$students = $students->findByClass($class);

	$num = 1;
	$pdf->SetFont('Arial', '', 10);
	foreach ($students as $student) {
		$pdf->Cell(10, 5, $num, 0, 0, "R");
		$pdf->Cell(15, 5, $student->id, 0, 0, "C");
		$pdf->Cell(50, 5, ucwords(utf8_decode($student->apellidos)));
		$pdf->Cell(40, 5, ucwords(utf8_decode($student->nombre)));
		$pdf->Cell(75, 5, "", "B", 0, "C");
		$pdf->Ln();
		$num++;
	}
}

$pdf->Output();
