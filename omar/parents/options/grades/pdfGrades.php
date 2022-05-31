<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\PDF;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$student = new Student($_POST['studentSS']);
$teacher = new Teacher();
$teacher = $teacher->findByGrade($student->grado);

$pdf = new PDF();
$pdf->SetTitle('Tarjeta de notas');
$pdf->Fill();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 40);
$pdf->SetTextColor(255, 192, 203);
$pdf->RotatedText(65, 235, 'Este documento no es oficial.', 65);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 5, 'Grades Report', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, Util::date(), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 10, 'STUDENT', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 10, $student->fullName(), 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 10, 'GROUP', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 10, $student->grado, 1, 1, 'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 10, 'HOMEROOM TEACHER', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 10, "$teacher->nombre $teacher->apellidos", 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 10, 'YEAR', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 10, $student->info('year'), 1, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(55, 5, 'COURSE', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'T1', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'T2', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'S1', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'T3', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'T4', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'S2', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'FINAL', 1, 1, 'C', true);
$pdf->SetFont('Times', 'B', 9);
foreach ($student->classes() as $class) {

    $grade = DB::table('padres')->where([
        ['ss', $student->ss],
        ['year', $student->info('year')],
        ['curso', $class->curso]
    ])->first();

    $sem1 = $sem2 = $fin = false;
    if ($grade->nota1 !== '' || $grade->nota2 !== '') {
        $sem1 = true;
    }
    if ($grade->nota3 !== '' || $grade->nota4 !== '') {
        $sem2 = true;
    }
    if ($grade->sem1 !== '' || $grade->sem2 !== '') {
        $fin = true;
    }


    $pdf->Cell(55, 10, utf8_decode($class->descripcion), 1, 0, 'L', true);
    $pdf->Cell(20, 10, $student->info('tri') >= 1 ? Util::numberToLetter($grade->nota1) : '', 1, 0, 'C');
    $pdf->Cell(20, 10, $student->info('tri') >= 2 ? Util::numberToLetter($grade->nota2) : '', 1, 0, 'C');
    $pdf->Cell(20, 10, $sem1 ? Util::numberToLetter($grade->sem1) : '', 1, 0, 'C');
    $pdf->Cell(20, 10, $student->info('tri') >= 3 ? Util::numberToLetter($grade->nota3) : '', 1, 0, 'C');
    $pdf->Cell(20, 10, $student->info('tri') >= 4 ? Util::numberToLetter($grade->nota4) : '', 1, 0, 'C');
    $pdf->Cell(20, 10, $sem2 ? Util::numberToLetter($grade->sem2) : '', 1, 0, 'C');
    $pdf->Cell(20, 10, $fin ? Util::numberToLetter($grade->final) : '', 1, 1, 'C');
}

$pdf->Output();
