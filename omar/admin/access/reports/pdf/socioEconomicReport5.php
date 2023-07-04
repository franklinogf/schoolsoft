<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
	['Resumen socioeconómico', 'Socioeconomic summary'],
	['Tamaño', 'Family'],
	['familia', 'Size'],
	['Ingreso maximo', 'Maximun income'],
	['de familia anual', 'family annual'],
	['Estudiantes', 'Students'],
	['sobre nivel', 'over level'],
	['bajo nivel', 'under level'],
	['Estudiantes sobre nivel por grado', 'Over level students by grade']
]);
$_grades = ['MA', 'PK', 'KG', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

$school = new School();
$year = $school->info('year');
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Resumen socioeconómico") . " $year", true);
$pdf->Fill();

$pdf->AddPage('L');
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Resumen socioeconómico") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(30, 5, $lang->translation("Tamaño"), 'LTR', 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Ingreso maximo"), 'TR', 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Estudiantes"), 'TR', 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Estudiantes"), 'TR', 0, 'C', true);
$pdf->Cell(count($_grades) * 10, 5, $lang->translation("Estudiantes sobre nivel por grado"), 'TR', 1, 'C', true);

$pdf->Cell(30, 5, $lang->translation("familia"), 'LBR', 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("de familia anual"), 'BR', 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("sobre nivel"), 'BR', 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("bajo nivel"), 'BR', 0, 'C', true);
foreach ($_grades as $grade) {
	$pdf->Cell(10, 5, $grade, 'TBR', 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

for ($familyAmount = 1; $familyAmount <= 10; $familyAmount++) {
	$levelAmount = [0, 0];
	$amountByGrade = [];
	$families = DB::table('madre')->where(['nfam', $familyAmount])->get();
	$socioEconomic = DB::table('socio_economico')->where(['dependientes', $familyAmount])->first();

	foreach ($families as $family) {
		$students = DB::table('year')->where([
			['year', $year],
			['id', $family->id]
		])->get();
		foreach ($students as $student) {
			$totalSalary = $family->sueldop + $family->sueldom;
			if ($totalSalary >= $socioEconomic->sobre_nivel) {
				$levelAmount[0]++;
				list($grade, $g2) = explode('-', $student->grado);
				foreach ($_grades as $_grade) {
					if($grade == $_grade){
						$amountByGrade[$familyAmount][$_grade]++;
					}
				}
			} else {
				$levelAmount[1]++;
			}
		}
	}
	if ($levelAmount[0] > 0 || $levelAmount[1] > 0) {
		$pdf->Cell(30, 5, $familyAmount, 1, 0, 'C');
		$pdf->Cell(30, 5, $socioEconomic->bajo_nivel, 1, 0, 'C');
		$pdf->Cell(30, 5, $levelAmount[0], 1, 0, 'C');
		$pdf->Cell(30, 5, $levelAmount[1], 1, 0, 'C');
		foreach($_grades as $_grade){
			$pdf->Cell(10, 5, $amountByGrade[$familyAmount][$_grade], 1, 0, 'C');
		}
		$pdf->Ln();
	}
}

$pdf->Output();
