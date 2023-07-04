<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Parents;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
	['Totales socioeconómico', 'Socioeconomic totals'],
	['Sobre nivel', 'Over level'],
	['Bajo nivel', 'Under level'],
	['Grado', 'Grade'],
	['Matrícula', 'Registration'],
	['Director', 'Principal'],
	['Fecha', 'Date'],
	['Porcentaje de estudiantes', 'Percentage of students'],
	['bajo nivel de pobreza', 'below poverty level'],
	['Total de estudiantes con ingresos', 'Total students with family'],
	['familiares de $3,000.00 o menos', 'income of $3,000.00 or less'],
]);

$school = new School();
$year = $school->info('year');
$_grades = $school->allGrades();

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Totales socioeconómico") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Totales socioeconómico") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Matrícula"), 1, 0, 'C', true);
$pdf->Cell(20, 5, "Total M", 1, 0, 'C', true);
$pdf->Cell(20, 5, "Total F", 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Sobre nivel"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Bajo nivel"), 1, 0, 'C', true);
$pdf->Cell(30, 5, "% " . $lang->translation("Bajo nivel"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$totals = [];
$salaryOf3000 = 0;
foreach ($_grades as $grade) {
	$students = DB::table('year')->where([
		['year', $year],
		['grado', $grade],
		['activo', ''],
	])->get();
	$totalsByGrade = ['over' => 0, 'under' => 0];
	foreach ($students as $student) {
		$parents = new Parents($student->id);
		$socioEconomic = DB::table('socio_economico')->where(['dependientes', $parents->nfam])->first();
		$totalSalary = $parents->sueldop + $parents->sueldom;
		if ($totalSalary > 0) {
			$totalsByGrade['registration']++;
			$totals['registration']++;
			if (Util::gender($student->genero) === 'M') {
				$totalsByGrade['M']++;
				$totals['M']++;
			} else if (Util::gender($student->genero) === 'F') {
				$totalsByGrade['F']++;
				$totals['F']++;
			}
			if ($totalSalary <= 3000) {
				$salaryOf3000++;
			}
			if ($totalSalary >= $socioEconomic->sobre_nivel) {
				$totalsByGrade['over']++;
				$totals['over']++;
			}
			if ($totalSalary <= $socioEconomic->bajo_nivel) {
				$totalsByGrade['under']++;
				$totals['under']++;
			}
		}
	}
	$pdf->Cell(20, 5, $grade, 1, 0, 'C');
	$pdf->Cell(30, 5, $totalsByGrade['registration'], 1, 0, 'C');
	$pdf->Cell(20, 5, $totalsByGrade["M"], 1, 0, 'C');
	$pdf->Cell(20, 5, $totalsByGrade["F"], 1, 0, 'C');
	$pdf->Cell(30, 5,  $totalsByGrade["over"], 1, 0, 'C');
	$pdf->Cell(30, 5,  $totalsByGrade["under"], 1, 0, 'C');

	$percent = $totalsByGrade['registration'] > 0 ? round(($totalsByGrade["under"] / $totalsByGrade["registration"]) * 100, 2) : '';

	$pdf->Cell(30, 5, $percent !== '' ? "$percent%" : '', 1, 1, 'C');
}
$pdf->Cell(20, 5, "Total", 1, 0, 'C');
$pdf->Cell(30, 5, $totals['registration'], 1, 0, 'C');
$pdf->Cell(20, 5, $totals['M'], 1, 0, 'C');
$pdf->Cell(20, 5, $totals['F'], 1, 0, 'C');
$pdf->Cell(30, 5, $totals['over'], 1, 0, 'C');
$pdf->Cell(30, 5, $totals['under'], 1, 0, 'C');
$percent = $totals['registration'] > 0 ? round(($totals["under"] / $totals["registration"]) * 100, 2) : '';

$pdf->Cell(30, 5, "$percent%", 1, 1, 'C');
$pdf->Ln(10);

$pdf->Cell(50, 5, "$percent%", 'B', 0, 'C');
$pdf->Cell(80);
$pdf->Cell(50, 5, "", 'B', 1, 'C');
$pdf->Cell(50, 5, $lang->translation("Porcentaje de estudiantes"), 0, 0, 'C');
$pdf->Cell(80);
$pdf->Cell(50, 5, $lang->translation("Director"), 0, 1, 'C');
$pdf->Cell(50, 5, $lang->translation("bajo nivel de pobreza"), 0, 1, 'C');
$pdf->Ln(10);

$pdf->Cell(130);
$pdf->Cell(50, 5, "", 'B', 1, 'C');
$pdf->Cell(50, 5, $lang->translation("Total de estudiantes con ingresos"));
$pdf->Cell(80);
$pdf->Cell(50, 5, $lang->translation("Fecha"), 0, 1, 'C');
$pdf->Cell(__LANG === 'es' ? 55 : 45, 5, $lang->translation("familiares de $3,000.00 o menos"));
$pdf->Cell(10, 4, $salaryOf3000, 'B', 0, 'C');


$pdf->Output();
