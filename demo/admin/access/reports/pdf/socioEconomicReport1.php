<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;

Session::is_logged();

$lang = new Lang([
	['Lista socioeconómico', 'Socioeconomic list'],
	['Bajo', 'Low'],
	['Medio', 'Medium'],
	['Alto', 'High'],
	['Cuenta', 'Account'],
	['Estudiante', 'Student'],
	['Sueldo', 'Salary'],
	['Nivel', 'Level'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista socioeconómico") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista socioeconómico") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
$pdf->Cell(85, 5, $lang->translation("Estudiante"), 1, 0, 'C', true);
$pdf->Cell(10, 5, 'G.F.', 1, 0, 'C', true);
$pdf->Cell(25, 5, $lang->translation("Sueldo"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Nivel"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);


$students = new Student();
$students = $students->all();
foreach ($students as $count => $student) {
	$parent = new Parents($student->id);
	$socioEconomic = DB::table('socio_economico')->where(['dependientes', $parent->nfam])->first();
	$totalSalary = $parent->sueldop + $parent->sueldom;
	$pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
	$pdf->Cell(20, 5, $student->id, 1, 0, 'C');
	$pdf->Cell(85, 5, "$student->nombre $student->apellidos", 1);
	$pdf->Cell(10, 5,  $parent->nfam !== 0 ?  $parent->nfam : '', 1, 0, 'C');
	$pdf->Cell(25, 5, "$" . number_format($totalSalary, 2), 1, 0, 'C');
	$level = 'N/A';
	if ($parent->nfam !== '' && $parent->nfam !== 0) {
		if ($totalSalary > 0) {
			if ($totalSalary <= $socioEconomic->bajo_nivel) {
				$level = $lang->translation("Bajo");
			} else if ($totalSalary >= $socioEconomic->bajo_nivel && $totalSalary <= $socioEconomic->sobre_nivel) {
				$level = $lang->translation("Medio");
			} else {
				$level = $lang->translation("Alto");
			}
		}
	}

	$pdf->Cell(20, 5, $level, 1, 1, 'C');
}

$pdf->Output();
