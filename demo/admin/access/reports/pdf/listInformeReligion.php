<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["LISTA TOTALES POR RELIGION", "TOTAL LIST BY RELIGION"],
    ["Adve", "Adve"],
    ["Grado", "Grade"],
    ["Baut", "Bapt"],
    ['Cató', 'Cath'],
    ['Evan', 'Evan'],
    ['Mita', 'Mita'],
    ['Nada', 'Nothing'],
    ['Meto', 'Meth'],
    ['Pent', 'Pent'],
    ['Lute', 'Luthe'],
    ['Fecha', 'Date'],
    ['Edad', 'Age'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$teacherClass = new Teacher();
$studentClass = new Student();

$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("LISTA TOTALES POR RELIGION") . " $year", true);
$pdf->Fill();
$pdf->AddPage();
$gr = 0;
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("LISTA TOTALES POR RELIGION") . " $year", 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Adve"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Baut"), 1, 0, 'C', true);
$pdf->Cell(18, 5, utf8_encode($lang->translation("Cató")), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Evan"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Mita"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Meto"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Pent"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Lute"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Nada"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    //    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $r1 = 0;
    $r2 = 0;
    $r3 = 0;
    $r4 = 0;
    $r5 = 0;
    $r6 = 0;
    $r7 = 0;
    $r8 = 0;
    $r9 = 0;
    foreach ($students as $count => $student) {
        //        list($anonaz, $mesnaz, $dianaz) = explode('-', $fec);
        if ($student->religion == 1) {
            $r1 = $r1 + 1;
        }
        if ($student->religion == 2) {
            $r2 = $r2 + 1;
        }
        if ($student->religion == 3) {
            $r3 = $r3 + 1;
        }
        if ($student->religion == 4) {
            $r4 = $r4 + 1;
        }
        if ($student->religion == 5) {
            $r5 = $r5 + 1;
        }
        if ($student->religion == 6) {
            $r6 = $r6 + 1;
        }
        if ($student->religion == 7) {
            $r7 = $r7 + 1;
        }
        if ($student->religion == 8) {
            $r8 = $r8 + 1;
        }
        if ($student->religion == 0) {
            $r9 = $r9 + 1;
        }
    }
    $gr = $gr + 1;
    $pdf->Cell(10, 5, $gr, 1, 0, 'C');
    $pdf->Cell(18, 5, $grade, 1, 0, 'C');
    $pdf->Cell(18, 5, $r1, 1, 0, 'C');
    $pdf->Cell(18, 5, $r2, 1, 0, 'C');
    $pdf->Cell(18, 5, $r3, 1, 0, 'C');
    $pdf->Cell(18, 5, $r4, 1, 0, 'C');
    $pdf->Cell(18, 5, $r5, 1, 0, 'C');
    $pdf->Cell(18, 5, $r6, 1, 0, 'C');
    $pdf->Cell(18, 5, $r7, 1, 0, 'C');
    $pdf->Cell(18, 5, $r8, 1, 0, 'C');
    $pdf->Cell(18, 5, $r9, 1, 1, 'C');
}

$pdf->Output();
