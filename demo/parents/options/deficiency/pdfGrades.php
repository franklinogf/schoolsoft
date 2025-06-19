<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$student = new Student($_POST['studentSS']);
$teacher = new Teacher();
$teacher = $teacher->findByGrade($student->grado);

$lang = new Lang([
    ["Informe de deficiencia", "Deficiency report"],
    ["Este documento no es oficial.", "This document is not official."],
    ['Informe de notas', 'Grades report']
]);

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de deficiencia"));
$pdf->Fill();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 40);
$pdf->SetTextColor(255, 192, 203);
//$pdf->RotatedText(65, 235, $lang->translation('Este documento no es oficial.'), 65);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 5, $lang->translation("Informe de deficiencia"), 0, 1, 'C');
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
$nom = $teacher->nombre ?? '';
$ape = $teacher->apellidos ?? '';
$pdf->Cell(95, 10, "$nom $ape", 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 10, 'YEAR', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 10, $student->info('year'), 1, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(65, 5, 'COURSE', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'T1', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'T2', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'T3', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'T4', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'CRTS', 1, 1, 'C', true);
$pdf->SetFont('Times', 'B', 9);
$fra = 0;
foreach ($student->classes() as $class) {

    $grade = DB::table('padres')->where([
        ['ss', $student->ss],
        ['year', $student->info('year')],
        ['curso', $class->curso]
    ])->first();

    $sem1 = $sem2 = $fin = $def = false;
    if ($grade->nota1 !== '' || $grade->nota2 !== '') {
        $sem1 = true;
        if ($grade->nota1 <= $student->info('vnf') and $grade->nota1 !== '' or $grade->nota2 <= $student->info('vnf') and $grade->nota2 !== '') {
            $def = true;
        }
    }
    if ($grade->nota3 !== '' || $grade->nota4 !== '') {
        $sem2 = true;
        if ($grade->nota3 <= $student->info('vnf') and $grade->nota3 !== '' or $grade->nota4 <= $student->info('vnf') and $grade->nota4 !== '') {
            $def = true;
        }
    }
    if ($grade->sem1 !== '' || $grade->sem2 !== '') {
        $fin = true;
    }

    if ($grade->nota1 <= $student->info('vnf') and $grade->nota1 !== '' and $student->info('fra') == 1) {
        $fra = $fra + 1;
    }
    if ($grade->nota2 <= $student->info('vnf') and $grade->nota2 !== '' and $student->info('fra') == 2) {
        $fra = $fra + 1;
    }
    if ($grade->nota3 <= $student->info('vnf') and $grade->nota3 !== '' and $student->info('fra') == 3) {
        $fra = $fra + 1;
    }
    if ($grade->nota4 <= $student->info('vnf') and $grade->nota4 !== '' and $student->info('fra') == 4) {
        $fra = $fra + 1;
    }

    if ($def) {
        $pdf->Cell(65, 10, $class->descripcion, 1, 0, 'L', true);
        $pdf->Cell(25, 10, $student->info('fra') >= 1 ? $grade->nota1 : '', 1, 0, 'C');
        $pdf->Cell(25, 10, $student->info('fra') >= 2 ? $grade->nota2 : '', 1, 0, 'C');
        $pdf->Cell(25, 10, $student->info('fra') >= 3 ? $grade->nota3 : '', 1, 0, 'C');
        $pdf->Cell(25, 10, $student->info('fra') >= 4 ? $grade->nota4 : '', 1, 0, 'C');
        $pdf->Cell(25, 10, $grade->credito, 1, 1, 'C');
    }
}

$ip = $_SERVER['REMOTE_ADDR'];
DB::table('acuse')->insert([
    'id' => $student->id,
    'ss' => $_POST['studentSS'],
    'grado' => $student->grado,
    'year' => $student->info('year'),
    'ip' => $ip,
    'hora' => date('h:i:s'),
    'fecha' => date('Y-m-d'),
    'tri' => $student->info('fra'),
    'hoja' => 2,
    'fra' => $fra ?? 0,
]);

$pdf->Output();
