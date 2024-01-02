<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Util;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

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

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de procedencia") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de procedencia") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

    $lugares = ['Ponce', 'Juncos', 'Las Piedras', 'Humacao', 'Gurabo', 'Naguabo', 'Yabucoa', 'Caguas', 'Canóvanas', 'Carolina', 'Trujillo Alto'];

    $pdf->Ln(5);
    $pdf->Cell(50, 15, 'Lugar de Procedencia', 1, 0, 'C', true);
    $pdf->Cell(40, 7.5, 'Cantidad de', 'LTR', 0, 'C', true);
    $pdf->Cell(30, 15, 'Por ciento %', 1, 0, 'C', true);
    $pdf->Cell(60, 5, utf8_decode('Género'), 1, 0, 'C', true);
    $pdf->Ln(7.5);
    $pdf->Cell(50);
    $pdf->Cell(40, 7.5, 'Estudiantes', 'LBR', 0, 'C', true);
    $pdf->Cell(30);
    $pdf->SetXY(130, $pdf->GetY() - 2.5);
    $pdf->Cell(30, 5, utf8_decode('Cuántos'), 'LTR', 0, 'C', true);
    $pdf->Cell(30, 5, utf8_decode('Cuántos'), 'LTR', 1, 'C', true);
    $pdf->Cell(120);
    $pdf->Cell(30, 5, 'Femenino', 'LBR', 0, 'C', true);
    $pdf->Cell(30, 5, 'Masculino', 'LBR', 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
    $lugarCount = [];
    $lugarTotal = [];
    foreach ($lugares as $lugar) {
        $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
        $lugarCount[$lugar] = 0;
        foreach ($students as $count => $student) {
            $parent = new Parents($student->id);
            if ($lugar == $parent->pueblo1 || $lugar == $parent->pueblo2) {
                $lugarCount[$lugar]++;
                $lugarTotal[$lugar]++;
                $gender = Util::gender($student->genero);
                $genderCount[$gender]++;
                $genderCount['T']++;
            }
        }
        // $porciento = $lugarTotal[$lugar] > 0  && $lugarCount[$lugar] / $lugarTotal[$lugar];
        $pdf->Cell(50, 5, utf8_decode($lugar), 1);
        $pdf->Cell(40, 5, $lugarCount[$lugar], 1, 0, 'C');
        $pdf->Cell(30, 5,  $lugarTotal[$lugar] > 0  ? ($lugarCount[$lugar] / $lugarTotal[$lugar]) * 100 . '%' : '', 1, 0, 'C');
        $pdf->Cell(30, 5, $genderCount['M'], 1, 0, 'C');
        $pdf->Cell(30, 5, $genderCount['F'], 1, 1, 'C');
    }
}

$pdf->Output();