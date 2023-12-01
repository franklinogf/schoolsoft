<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Controllers\Parents;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Informe de procedencia", "Provenance report"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Edad', 'Age'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['S.S.', 'S.S.'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year');
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de procedencia"). " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de procedencia") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

	$lugares = array('Juncos', 'Las Piedras', 'Humacao', 'Gurabo', 'Naguabo', 'Yabucoa', 'Caguas', 'Can&#65533;vanas', 'Carolina', 'Trujillo Alto');

	$pdf->Ln(5);
	$pdf->Cell(50, 15, 'Lugar de Procedencia', 1, 0, 'C', true);
	$pdf->Cell(40, 7.5, 'Cantidad de', 'LTR', 0, 'C', true);
	$pdf->Cell(30, 15, 'Por ciento %', 1, 0, 'C', true);
    $pdf->Cell(60, 5, 'Género', 1, 0, 'C', true);
	$pdf->Ln(7.5);
	$pdf->Cell(50);
	$pdf->Cell(40, 7.5, 'Estudiantes', 'LBR', 0, 'C', true);
	$pdf->Cell(30);
	$pdf->SetXY(130, $pdf->GetY() - 2.5);
    $pdf->Cell(30, 5, 'Cuántos', 'LTR', 0, 'C', true);
    $pdf->Cell(30, 5, 'Cuántos', 'LTR', 1, 'C', true);
	$pdf->Cell(120);
	$pdf->Cell(30, 5, 'Femenino', 'LBR', 0, 'C', true);
	$pdf->Cell(30, 5, 'Masculino', 'LBR', 1, 'C', true);



    foreach ($students as $count => $student) {


//$parent = DB::table('madre')->where([
//    ['id', $student->id],
//])->orderBy('id')->first();


        $gender = Util::gender($student->genero);
        $genderCount[$gender]++;
        $genderCount['T']++;
    }
    $pdf->SetFont('Arial', '', 10);
//    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
//    $pdf->Cell(18, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
//    $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
//    $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $lang->translation("Genero"), 1, 0, 'C', true);
//    $pdf->Cell(20, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $lang->translation("Edad"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $lang->translation("S.S."), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);


//    $pdf->Ln(2);
//    $pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $genderCount['T'], 1, 1, 'C');
//    $pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
//    $pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');
}




$pdf->Output();
